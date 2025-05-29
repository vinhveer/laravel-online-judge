package main

import (
    "log"
    "net/http"
    "os"

    "judge/api"
    "judge/db"
)

func main() {
    // Initialise DB connection (dsn from env MYSQL_DSN)
    db.InitDB("")

    // Register routes
    http.HandleFunc("/create_test", api.CreateTestHandler)
    http.HandleFunc("/list_testcases", api.ListTestCasesHandler)
    http.HandleFunc("/download_testcases", api.DownloadTestCasesHandler)

    addr := ":8081"
    if v := os.Getenv("SERVER_ADDR"); v != "" {
        addr = v
    }

    log.Printf("Server listening on %s", addr)
    if err := http.ListenAndServe(addr, nil); err != nil {
        log.Fatal(err)
    }
}
