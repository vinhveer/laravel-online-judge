package internal

import (
	"crypto/md5"
	"encoding/hex"
	"fmt"
	"judge/db"
	"judge/dbmodel"
	"os"
	"os/exec"
	"path/filepath"
	"strings"
	"time"
)

type SubmissionResult struct {
	Status       string       `json:"status"`
	Score        float64      `json:"score"`
	ErrorMessage string       `json:"error_message,omitempty"`
	TestResults  []TestResult `json:"test_results"`
}

type TestResult struct {
	TestID       int    `json:"test_id"`
	Status       string `json:"status"`
	TimeUsed     int32  `json:"time_used"`
	MemoryUsed   int32  `json:"memory_used"`
	ErrorMessage string `json:"error_message,omitempty"`
	Output       string `json:"output,omitempty"`
	Score        int32  `json:"score"`
}

func CreateSubmission(userID, problemID, sourceCode, language string) (*dbmodel.Submission, error) {
	submission := &dbmodel.Submission{
		UserID:      userID,
		ProblemID:   problemID,
		Code:        sourceCode,
		Language:    language,
		Status:      "Pending",
		SubmittedAt: time.Now(),
	}

	result := db.GetDB().Create(submission)
	if result.Error != nil {
		return nil, result.Error
	}

	// Get test cases and create submission test cases
	testCases, err := getTestCases(problemID)
	if err != nil {
		return nil, err
	}

	// Create submission test cases
	for _, testCase := range testCases {
		submissionTestCase := &dbmodel.SubmissionTestcase{
			SubmissionID: submission.ID,
			TestcaseName: testCase.Name,
			Status:       "Pending",
			JudgedAt:     time.Now(),
		}
		if err := db.GetDB().Create(submissionTestCase).Error; err != nil {
			return nil, err
		}
	}

	return submission, nil
}

func JudgeSubmission(submissionID int64) (*SubmissionResult, error) {
	var submission dbmodel.Submission
	if err := db.GetDB().First(&submission, submissionID).Error; err != nil {
		return nil, err
	}

	var problem dbmodel.Problem
	if err := db.GetDB().First(&problem, submission.ProblemID).Error; err != nil {
		return nil, err
	}

	// Create temporary directory for submission
	tempDir, err := os.MkdirTemp("", fmt.Sprintf("submission_%d_*", submissionID))
	if err != nil {
		return nil, err
	}
	defer os.RemoveAll(tempDir)

	// Write source code to file
	sourceFile := filepath.Join(tempDir, getSourceFileName(submission.Language))
	if err := os.WriteFile(sourceFile, []byte(submission.Code), 0644); err != nil {
		return nil, err
	}

	// Compile if needed
	if err := compileCode(sourceFile, submission.Language); err != nil {
		return &SubmissionResult{
			Status:       "Compile Error",
			ErrorMessage: err.Error(),
		}, nil
	}

	// Get test cases
	testCases, err := getTestCases(problem.ID)
	if err != nil {
		return nil, err
	}

	// Judge each test case
	var testResults []TestResult
	totalScore := 0.0

	for _, testCase := range testCases {
		result := judgeTestCase(sourceFile, submission.Language, testCase, problem.TimeLimit, problem.MemoryLimit)
		testResults = append(testResults, result)

		if result.Status == "Accepted" {
			totalScore += 1.0
		}

		// Update submission status after each test
		submission.Status = getOverallStatus(testResults)
		submission.Score = int32(totalScore * 100 / float64(len(testCases)))
		db.GetDB().Save(&submission)
	}

	return &SubmissionResult{
		Status:      submission.Status,
		Score:       totalScore * 100 / float64(len(testCases)),
		TestResults: testResults,
	}, nil
}

func getSourceFileName(language string) string {
	switch language {
	case "cpp":
		return "main.cpp"
	case "python":
		return "main.py"
	case "java":
		return "Main.java"
	default:
		return "main"
	}
}

func compileCode(sourceFile, language string) error {
	switch language {
	case "cpp":
		cmd := exec.Command("g++", "-std=c++17", sourceFile, "-o", strings.TrimSuffix(sourceFile, ".cpp"))
		return cmd.Run()
	case "java":
		cmd := exec.Command("javac", sourceFile)
		return cmd.Run()
	case "python":
		// Python doesn't need compilation
		return nil
	default:
		return fmt.Errorf("unsupported language: %s", language)
	}
}

func judgeTestCase(sourceFile, language string, testCase TestCase, timeLimit, memoryLimit int32) TestResult {
	// Create input file
	inputFile := filepath.Join(filepath.Dir(sourceFile), "input.txt")
	if err := os.WriteFile(inputFile, []byte(testCase.Input), 0644); err != nil {
		return TestResult{
			Status:       "System Error",
			ErrorMessage: "Failed to create input file",
		}
	}

	// Create output file
	outputFile := filepath.Join(filepath.Dir(sourceFile), "output.txt")
	output, err := os.Create(outputFile)
	if err != nil {
		return TestResult{
			Status:       "System Error",
			ErrorMessage: "Failed to create output file",
		}
	}
	defer output.Close()

	// Prepare command
	var cmd *exec.Cmd
	switch language {
	case "cpp":
		cmd = exec.Command(strings.TrimSuffix(sourceFile, ".cpp"))
	case "python":
		cmd = exec.Command("python3", sourceFile)
	case "java":
		cmd = exec.Command("java", "-cp", filepath.Dir(sourceFile), "Main")
	}

	// Set up input/output
	cmd.Stdin, _ = os.Open(inputFile)
	cmd.Stdout = output
	cmd.Stderr = output

	// Run with timeout
	startTime := time.Now()
	err = cmd.Start()
	if err != nil {
		return TestResult{
			Status:       "Runtime Error",
			ErrorMessage: err.Error(),
		}
	}

	done := make(chan error)
	go func() {
		done <- cmd.Wait()
	}()

	select {
	case err := <-done:
		if err != nil {
			return TestResult{
				Status:       "Runtime Error",
				ErrorMessage: err.Error(),
			}
		}
	case <-time.After(time.Duration(timeLimit) * time.Millisecond):
		cmd.Process.Kill()
		return TestResult{
			Status: "Time Limit Exceeded",
		}
	}

	// Check output
	outputContent, err := os.ReadFile(outputFile)
	if err != nil {
		return TestResult{
			Status:       "System Error",
			ErrorMessage: "Failed to read output file",
		}
	}

	// Compare outputs
	if compareOutputs(string(outputContent), testCase.Output) {
		return TestResult{
			Status:     "Accepted",
			TimeUsed:   int32(time.Since(startTime).Milliseconds()),
			MemoryUsed: 0, // TODO: Implement memory tracking
		}
	}

	return TestResult{
		Status:     "Wrong Answer",
		TimeUsed:   int32(time.Since(startTime).Milliseconds()),
		MemoryUsed: 0, // TODO: Implement memory tracking
	}
}

func compareOutputs(actual, expected string) bool {
	// Remove whitespace and newlines
	actual = strings.TrimSpace(actual)
	expected = strings.TrimSpace(expected)

	// Calculate MD5 hashes
	actualHash := md5.Sum([]byte(actual))
	expectedHash := md5.Sum([]byte(expected))

	return hex.EncodeToString(actualHash[:]) == hex.EncodeToString(expectedHash[:])
}

func getOverallStatus(results []TestResult) string {
	if len(results) == 0 {
		return "Pending"
	}

	// Check for system errors first
	for _, r := range results {
		if r.Status == "System Error" {
			return "System Error"
		}
	}

	// Check for compile errors
	if results[0].Status == "Compile Error" {
		return "Compile Error"
	}

	// Check if all tests are accepted
	allAccepted := true
	for _, r := range results {
		if r.Status != "Accepted" {
			allAccepted = false
			break
		}
	}
	if allAccepted {
		return "Accepted"
	}

	// Return the first non-accepted status
	for _, r := range results {
		if r.Status != "Accepted" {
			return r.Status
		}
	}

	return "Pending"
}

type TestCase struct {
	Name   string
	Input  string
	Output string
}

func getTestCase(problemID, testcaseName string) (TestCase, error) {
	// TODO: Implement getting test case from database
	// For now, return dummy test case
	return TestCase{
		Name:   testcaseName,
		Input:  "1 2",
		Output: "3",
	}, nil
}

func getTestCases(problemID string) ([]TestCase, error) {
	// TODO: Implement getting test cases from database
	// For now, return dummy test cases
	return []TestCase{
		{
			Name:   "test1",
			Input:  "1 2",
			Output: "3",
		},
		{
			Name:   "test2",
			Input:  "2 3",
			Output: "5",
		},
	}, nil
}

func JudgeSubmissionTestCase(submissionID int64, testcaseName string) (TestResult, error) {
	var submission dbmodel.Submission
	if err := db.GetDB().First(&submission, submissionID).Error; err != nil {
		return TestResult{}, err
	}

	var submissionTestCase dbmodel.SubmissionTestcase
	if err := db.GetDB().Where("submission_id = ? AND testcase_name = ?", submissionID, testcaseName).First(&submissionTestCase).Error; err != nil {
		return TestResult{}, err
	}

	var problem dbmodel.Problem
	if err := db.GetDB().First(&problem, submission.ProblemID).Error; err != nil {
		return TestResult{}, err
	}

	// Create temporary directory for submission
	tempDir, err := os.MkdirTemp("", fmt.Sprintf("submission_%d_*", submissionID))
	if err != nil {
		return TestResult{}, err
	}
	defer os.RemoveAll(tempDir)

	// Write source code to file
	sourceFile := filepath.Join(tempDir, getSourceFileName(submission.Language))
	if err := os.WriteFile(sourceFile, []byte(submission.Code), 0644); err != nil {
		return TestResult{}, err
	}

	// Compile if needed
	if err := compileCode(sourceFile, submission.Language); err != nil {
		submissionTestCase.Status = "Compile Error"
		submissionTestCase.ErrorMessage = err.Error()
		submissionTestCase.JudgedAt = time.Now()
		db.GetDB().Save(&submissionTestCase)
		return TestResult{
			TestID:       int(submissionTestCase.ID),
			Status:       "Compile Error",
			ErrorMessage: err.Error(),
		}, nil
	}

	// Get test case
	testCase, err := getTestCase(problem.ID, testcaseName)
	if err != nil {
		return TestResult{}, err
	}

	// Judge test case
	result := judgeTestCase(sourceFile, submission.Language, testCase, problem.TimeLimit, problem.MemoryLimit)

	// Update submission test case
	submissionTestCase.Status = result.Status
	submissionTestCase.ExecutionTime = result.TimeUsed
	submissionTestCase.MemoryUsed = result.MemoryUsed
	submissionTestCase.Output = result.Output
	submissionTestCase.ErrorMessage = result.ErrorMessage
	submissionTestCase.Score = result.Score
	submissionTestCase.JudgedAt = time.Now()
	db.GetDB().Save(&submissionTestCase)

	// Update overall submission status
	updateSubmissionStatus(submissionID)

	return result, nil
}

func updateSubmissionStatus(submissionID int64) {
	var submission dbmodel.Submission
	if err := db.GetDB().First(&submission, submissionID).Error; err != nil {
		return
	}

	var testCases []dbmodel.SubmissionTestcase
	if err := db.GetDB().Where("submission_id = ?", submissionID).Find(&testCases).Error; err != nil {
		return
	}

	// Calculate overall status and score
	totalScore := 0
	allAccepted := true
	for _, tc := range testCases {
		if tc.Status == "Accepted" {
			totalScore += int(tc.Score)
		} else {
			allAccepted = false
		}
	}

	submission.Score = int32(totalScore)
	if allAccepted {
		submission.Status = "Accepted"
	} else {
		submission.Status = "Wrong Answer"
	}
	db.GetDB().Save(&submission)
}
