package internal

import (
	"archive/zip"
	"bytes"
	"errors"
	"fmt"
	"io"
	"os"
	"path/filepath"
	"regexp"
)

// CreateTest receives a problem id and the raw bytes of a zip file that contains
// testcase inputs/outputs, validates its structure and extracts the files into
// a directory test_cases/<problemID>/.
// It returns the number of test pairs extracted (e.g. 3 means 3.in/3.out) or an error.
func CreateTest(problemID string, zipData []byte) (int, error) {
	if len(zipData) == 0 {
		return 0, errors.New("empty zip data")
	}

	// Open zip reader from byte slice
	zr, err := zip.NewReader(bytes.NewReader(zipData), int64(len(zipData)))
	if err != nil {
		return 0, fmt.Errorf("invalid zip file: %w", err)
	}

	// Collect filenames and validate naming pattern
	// Allowed pattern: <number>.in or <number>.out
	pattern := regexp.MustCompile(`^([0-9]+)\.(in|out)$`)
	inputs := make(map[string]struct{})
	outputs := make(map[string]struct{})

	for _, f := range zr.File {
		if f.FileInfo().IsDir() {
			continue // skip directories
		}
		name := filepath.Base(f.Name) // ignore folders inside the zip
		matches := pattern.FindStringSubmatch(name)
		if matches == nil {
			return 0, fmt.Errorf("invalid testcase filename: %s", name)
		}
		idx := matches[1]
		ext := matches[2]

		if ext == "in" {
			inputs[idx] = struct{}{}
		} else {
			outputs[idx] = struct{}{}
		}
	}

	if len(inputs) == 0 {
		return 0, errors.New("no testcase files found in zip")
	}

	// Ensure each input has corresponding output and vice-versa
	for idx := range inputs {
		if _, ok := outputs[idx]; !ok {
			return 0, fmt.Errorf("missing output file for testcase %s", idx)
		}
	}
	for idx := range outputs {
		if _, ok := inputs[idx]; !ok {
			return 0, fmt.Errorf("missing input file for testcase %s", idx)
		}
	}

	// Prepare destination directory
	destDir := filepath.Join("test_cases", problemID)
	// Remove older testcases if they exist – keeps only latest upload.
	_ = os.RemoveAll(destDir)
	if err := os.MkdirAll(destDir, 0o755); err != nil {
		return 0, fmt.Errorf("cannot create destination dir: %w", err)
	}

	// Extract all files
	for _, f := range zr.File {
		if f.FileInfo().IsDir() {
			continue
		}
		name := filepath.Base(f.Name)
		destPath := filepath.Join(destDir, name)

		rc, err := f.Open()
		if err != nil {
			return 0, fmt.Errorf("cannot open file %s in zip: %w", name, err)
		}
		dst, err := os.Create(destPath)
		if err != nil {
			rc.Close()
			return 0, fmt.Errorf("cannot create file %s: %w", destPath, err)
		}
		if _, err := io.Copy(dst, rc); err != nil {
			rc.Close()
			dst.Close()
			return 0, fmt.Errorf("cannot write file %s: %w", destPath, err)
		}
		rc.Close()
		dst.Close()
	}

	return len(inputs), nil
}

// ListTestCases reads the testcase files for the given problemID from the test_cases directory.
// It returns a map where keys are testcase indices (e.g. "1", "2") and values are maps with "in" and "out" keys containing file contents.
func ListTestCases(problemID string) (map[string]map[string]string, error) {
	baseDir := filepath.Join("test_cases", problemID)
	if _, err := os.Stat(baseDir); os.IsNotExist(err) {
		return nil, fmt.Errorf("no testcases found for problem %s", problemID)
	}

	entries, err := os.ReadDir(baseDir)
	if err != nil {
		return nil, fmt.Errorf("cannot read testcase directory: %w", err)
	}

	result := make(map[string]map[string]string)
	for _, entry := range entries {
		if entry.IsDir() {
			continue
		}
		name := entry.Name()
		matches := regexp.MustCompile(`^([0-9]+)\.(in|out)$`).FindStringSubmatch(name)
		if matches == nil {
			continue
		}
		idx := matches[1]
		ext := matches[2]

		if _, ok := result[idx]; !ok {
			result[idx] = make(map[string]string)
		}

		data, err := os.ReadFile(filepath.Join(baseDir, name))
		if err != nil {
			return nil, fmt.Errorf("cannot read file %s: %w", name, err)
		}
		result[idx][ext] = string(data)
	}

	return result, nil
}

// GetTestCasesZip creates a zip archive containing all testcase files for the given problemID.
// It returns the zip data as a byte slice.
func GetTestCasesZip(problemID string) ([]byte, error) {
	baseDir := filepath.Join("test_cases", problemID)
	if _, err := os.Stat(baseDir); os.IsNotExist(err) {
		return nil, fmt.Errorf("no testcases found for problem %s", problemID)
	}

	entries, err := os.ReadDir(baseDir)
	if err != nil {
		return nil, fmt.Errorf("cannot read testcase directory: %w", err)
	}

	var buf bytes.Buffer
	zw := zip.NewWriter(&buf)

	for _, entry := range entries {
		if entry.IsDir() {
			continue
		}
		name := entry.Name()
		matches := regexp.MustCompile(`^([0-9]+)\.(in|out)$`).FindStringSubmatch(name)
		if matches == nil {
			continue
		}

		data, err := os.ReadFile(filepath.Join(baseDir, name))
		if err != nil {
			return nil, fmt.Errorf("cannot read file %s: %w", name, err)
		}

		f, err := zw.Create(name)
		if err != nil {
			return nil, fmt.Errorf("cannot create zip entry %s: %w", name, err)
		}
		if _, err := f.Write(data); err != nil {
			return nil, fmt.Errorf("cannot write zip entry %s: %w", name, err)
		}
	}

	if err := zw.Close(); err != nil {
		return nil, fmt.Errorf("cannot finalize zip: %w", err)
	}

	return buf.Bytes(), nil
}

// DeleteTestCase deletes test cases for the given problemID.
// If testID is empty, it deletes all test cases by removing the entire directory.
// If testID is provided, it deletes only the specific test case (both .in and .out files).
// Returns error if the test case doesn't exist or if there's an error during deletion.
func DeleteTestCase(problemID string, testID string) error {
	baseDir := filepath.Join("test_cases", problemID)
	if _, err := os.Stat(baseDir); os.IsNotExist(err) {
		return fmt.Errorf("no testcases found for problem %s", problemID)
	}

	// If testID is empty, delete the entire directory
	if testID == "" {
		if err := os.RemoveAll(baseDir); err != nil {
			return fmt.Errorf("cannot delete test cases directory: %w", err)
		}
		return nil
	}

	// Delete specific test case files
	inFile := filepath.Join(baseDir, testID+".in")
	outFile := filepath.Join(baseDir, testID+".out")

	// Check if at least one file exists
	if _, err := os.Stat(inFile); os.IsNotExist(err) {
		if _, err := os.Stat(outFile); os.IsNotExist(err) {
			return fmt.Errorf("test case %s not found for problem %s", testID, problemID)
		}
	}

	// Delete input file if exists
	if err := os.Remove(inFile); err != nil && !os.IsNotExist(err) {
		return fmt.Errorf("cannot delete input file: %w", err)
	}

	// Delete output file if exists
	if err := os.Remove(outFile); err != nil && !os.IsNotExist(err) {
		return fmt.Errorf("cannot delete output file: %w", err)
	}

	return nil
}
