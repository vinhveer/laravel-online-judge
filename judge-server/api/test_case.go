package api

import (
	"encoding/json"
	"fmt"
	"io"
	"log"
	"net/http"

	"judge/internal"
)

const maxUploadSize = 10 * 1024 * 1024 // 10 MB

// CreateTestHandler handles POST /create_test requests.
// The request must be multipart/form-data containing:
//   - field "id" : problem ID (string/int)
//   - file field "file": zip archive (<=10MB)
func CreateTestHandler(w http.ResponseWriter, r *http.Request) {
	if r.Method != http.MethodPost {
		w.WriteHeader(http.StatusMethodNotAllowed)
		return
	}

	// Enforce maximum size (slightly larger to allow form overhead)
	r.Body = http.MaxBytesReader(w, r.Body, maxUploadSize+1024)

	if err := r.ParseMultipartForm(maxUploadSize); err != nil {
		http.Error(w, "Request too large or invalid form", http.StatusBadRequest)
		return
	}

	problemID := r.FormValue("id")
	if problemID == "" {
		http.Error(w, "missing problem id", http.StatusBadRequest)
		return
	}

	file, header, err := r.FormFile("file")
	if err != nil {
		http.Error(w, "missing file field", http.StatusBadRequest)
		return
	}
	defer file.Close()

	if header.Size > maxUploadSize {
		http.Error(w, "file exceeds 10MB limit", http.StatusRequestEntityTooLarge)
		return
	}

	// Read entire zip into memory (<=10MB)
	data, err := io.ReadAll(file)
	if err != nil {
		http.Error(w, "cannot read uploaded file", http.StatusInternalServerError)
		return
	}

	count, err := internal.CreateTest(problemID, data)
	if err != nil {
		log.Println("create test error:", err)
		http.Error(w, err.Error(), http.StatusBadRequest)
		return
	}

	resp := struct {
		Status  string `json:"status"`
		Message string `json:"message"`
		Count   int    `json:"count"`
	}{
		Status:  "success",
		Message: "testcases imported successfully",
		Count:   count,
	}

	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(resp)
}

// ListTestCasesHandler handles GET /list_testcases?id=<problemID> requests.
// It returns a JSON response with a map of testcase indices to maps containing "in" and "out" file contents.
func ListTestCasesHandler(w http.ResponseWriter, r *http.Request) {
	if r.Method != http.MethodGet {
		w.WriteHeader(http.StatusMethodNotAllowed)
		return
	}

	problemID := r.URL.Query().Get("id")
	if problemID == "" {
		http.Error(w, "missing problem id", http.StatusBadRequest)
		return
	}

	testcases, err := internal.ListTestCases(problemID)
	if err != nil {
		log.Println("list testcases error:", err)
		http.Error(w, err.Error(), http.StatusBadRequest)
		return
	}

	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(testcases)
}

// DownloadTestCasesHandler handles GET /download_testcases?id=<problemID> requests.
// It returns a zip archive containing all testcase files for the given problemID.
func DownloadTestCasesHandler(w http.ResponseWriter, r *http.Request) {
	if r.Method != http.MethodGet {
		w.WriteHeader(http.StatusMethodNotAllowed)
		return
	}

	problemID := r.URL.Query().Get("id")
	if problemID == "" {
		http.Error(w, "missing problem id", http.StatusBadRequest)
		return
	}

	zipData, err := internal.GetTestCasesZip(problemID)
	if err != nil {
		log.Println("download testcases error:", err)
		http.Error(w, err.Error(), http.StatusBadRequest)
		return
	}

	w.Header().Set("Content-Type", "application/zip")
	w.Header().Set("Content-Disposition", fmt.Sprintf("attachment; filename=testcases_%s.zip", problemID))
	w.Write(zipData)
}

// DeleteTestCaseHandler handles DELETE /delete_testcase?id=<problemID>&test_id=<testID> requests.
// If test_id is not provided, it deletes all test cases for the problem.
// If test_id is provided, it deletes only the specified test case.
func DeleteTestCaseHandler(w http.ResponseWriter, r *http.Request) {
	w.Header().Set("Content-Type", "application/json")

	if r.Method != http.MethodDelete {
		w.WriteHeader(http.StatusMethodNotAllowed)
		json.NewEncoder(w).Encode(map[string]string{
			"error": "method not allowed",
		})
		return
	}

	problemID := r.URL.Query().Get("id")
	if problemID == "" {
		w.WriteHeader(http.StatusBadRequest)
		json.NewEncoder(w).Encode(map[string]string{
			"error": "missing problem id",
		})
		return
	}

	testID := r.URL.Query().Get("test_id")
	err := internal.DeleteTestCase(problemID, testID)
	if err != nil {
		log.Println("delete testcase error:", err)
		w.WriteHeader(http.StatusBadRequest)
		json.NewEncoder(w).Encode(map[string]string{
			"error": err.Error(),
		})
		return
	}

	message := "test case deleted successfully"
	if testID == "" {
		message = "all test cases deleted successfully"
	}

	resp := struct {
		Status  string `json:"status"`
		Message string `json:"message"`
	}{
		Status:  "success",
		Message: message,
	}

	json.NewEncoder(w).Encode(resp)
}
