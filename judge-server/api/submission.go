package api

import (
	"encoding/json"
	"io"
	"net/http"
	"strconv"

	"judge/db"
	"judge/dbmodel"
	"judge/internal"

	"github.com/gorilla/mux"
)

func CreateSubmissionHandler(w http.ResponseWriter, r *http.Request) {
	if r.Method != http.MethodPost {
		http.Error(w, "Method not allowed", http.StatusMethodNotAllowed)
		return
	}

	// Parse multipart form
	if err := r.ParseMultipartForm(32 << 20); err != nil { // 32MB max
		http.Error(w, "Failed to parse form: "+err.Error(), http.StatusBadRequest)
		return
	}

	// Get form values
	userID := r.FormValue("user_id")
	problemID := r.FormValue("problem_id")
	language := r.FormValue("language")

	// Get source code file
	file, _, err := r.FormFile("source_code")
	if err != nil {
		http.Error(w, "Failed to get source code file: "+err.Error(), http.StatusBadRequest)
		return
	}
	defer file.Close()

	// Read file content
	sourceCode, err := io.ReadAll(file)
	if err != nil {
		http.Error(w, "Failed to read source code file: "+err.Error(), http.StatusInternalServerError)
		return
	}

	// Validate request
	if userID == "" || problemID == "" || language == "" {
		http.Error(w, "Missing required fields", http.StatusBadRequest)
		return
	}

	// Validate language
	switch language {
	case "cpp", "python", "java":
		// Valid languages
	default:
		http.Error(w, "Invalid language. Supported languages: cpp, python, java", http.StatusBadRequest)
		return
	}

	// Create submission
	submission, err := internal.CreateSubmission(userID, problemID, string(sourceCode), language)
	if err != nil {
		http.Error(w, "Failed to create submission: "+err.Error(), http.StatusInternalServerError)
		return
	}

	// Return success response
	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(map[string]interface{}{
		"status": "success",
		"data": map[string]interface{}{
			"submission_id": submission.ID,
			"status":        submission.Status,
		},
	})
}

func JudgeSubmissionTestCaseHandler(w http.ResponseWriter, r *http.Request) {
	if r.Method != http.MethodPost {
		http.Error(w, "Method not allowed", http.StatusMethodNotAllowed)
		return
	}

	vars := mux.Vars(r)
	submissionID, err := strconv.ParseInt(vars["id"], 10, 64)
	if err != nil {
		http.Error(w, "Invalid submission ID", http.StatusBadRequest)
		return
	}

	testcaseName := vars["test_name"]
	if testcaseName == "" {
		http.Error(w, "Missing test case name", http.StatusBadRequest)
		return
	}

	result, err := internal.JudgeSubmissionTestCase(submissionID, testcaseName)
	if err != nil {
		http.Error(w, "Failed to judge submission test case: "+err.Error(), http.StatusInternalServerError)
		return
	}

	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(map[string]interface{}{
		"status": "success",
		"data":   result,
	})
}

func GetSubmissionResultHandler(w http.ResponseWriter, r *http.Request) {
	if r.Method != http.MethodGet {
		http.Error(w, "Method not allowed", http.StatusMethodNotAllowed)
		return
	}

	vars := mux.Vars(r)
	submissionID, err := strconv.ParseInt(vars["id"], 10, 64)
	if err != nil {
		http.Error(w, "Invalid submission ID", http.StatusBadRequest)
		return
	}

	var submission dbmodel.Submission
	if err := db.GetDB().First(&submission, submissionID).Error; err != nil {
		http.Error(w, "Failed to get submission: "+err.Error(), http.StatusInternalServerError)
		return
	}

	var testCases []dbmodel.SubmissionTestcase
	if err := db.GetDB().Where("submission_id = ?", submissionID).Find(&testCases).Error; err != nil {
		http.Error(w, "Failed to get test cases: "+err.Error(), http.StatusInternalServerError)
		return
	}

	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(map[string]interface{}{
		"status": "success",
		"data": map[string]interface{}{
			"submission_id": submission.ID,
			"status":        submission.Status,
			"score":         submission.Score,
			"test_cases":    testCases,
		},
	})
}
