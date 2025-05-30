package main

import (
	"log"
	"net/http"
	"os"

	"judge/api"
	"judge/db"

	"github.com/gorilla/mux"
)

func main() {
	// Initialise DB connection (dsn from env MYSQL_DSN)
	db.InitDB("")

	// Create router
	router := mux.NewRouter()

	// Register test case routes
	http.HandleFunc("/create_test", api.CreateTestHandler)
	http.HandleFunc("/list_testcases", api.ListTestCasesHandler)
	http.HandleFunc("/download_testcases", api.DownloadTestCasesHandler)
	http.HandleFunc("/delete_testcase", api.DeleteTestCaseHandler)

	// Register submission routes
	http.HandleFunc("/create_submission", api.CreateSubmissionHandler)
	http.HandleFunc("/submission/{id}", api.GetSubmissionResultHandler)

	// Mount the router
	http.Handle("/", router)

	addr := ":8081"
	if v := os.Getenv("SERVER_ADDR"); v != "" {
		addr = v
	}

	log.Printf("Server listening on %s", addr)
	if err := http.ListenAndServe(addr, nil); err != nil {
		log.Fatal(err)
	}
}
