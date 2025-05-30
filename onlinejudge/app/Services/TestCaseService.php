<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TestCaseService
{
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = 'http://localhost:8081';
    }

    public function listTestCases($problemId)
    {
        $response = Http::get("{$this->baseUrl}/list_testcases", [
            'id' => $problemId
        ]);

        if (!$response->successful()) {
            return [];
        }

        return $response->json();
    }

    public function uploadTestCases($problemId, $file)
    {
        $response = Http::attach(
            'file',
            file_get_contents($file),
            basename($file)
        )->post("{$this->baseUrl}/create_test", [
            'id' => $problemId
        ]);

        if (!$response->successful()) {
            throw new \Exception('Failed to upload test cases');
        }

        return $response->json();
    }

    public function downloadTestCases($problemId)
    {
        $response = Http::get("{$this->baseUrl}/download_testcases", [
            'id' => $problemId
        ]);

        if (!$response->successful()) {
            throw new \Exception('Failed to download test cases');
        }

        return $response->body();
    }

    public function deleteAllTestCases($problemId)
    {
        Log::info('Attempting to delete all test cases', [
            'problem_id' => $problemId,
            'url' => "{$this->baseUrl}/delete_testcase?id={$problemId}"
        ]);

        $response = Http::delete("{$this->baseUrl}/delete_testcase?id={$problemId}");

        Log::info('Delete test cases response', [
            'status' => $response->status(),
            'body' => $response->body(),
            'json' => $response->json()
        ]);

        if (!$response->successful()) {
            throw new \Exception('Failed to delete test cases: ' . $response->body());
        }

        return $response->json();
    }

    public function deleteTestCase($problemId, $testId)
    {
        Log::info('Attempting to delete single test case', [
            'problem_id' => $problemId,
            'test_id' => $testId,
            'url' => "{$this->baseUrl}/delete_testcase?id={$problemId}&test_id={$testId}"
        ]);

        $response = Http::delete("{$this->baseUrl}/delete_testcase?id={$problemId}&test_id={$testId}");

        Log::info('Delete single test case response', [
            'status' => $response->status(),
            'body' => $response->body(),
            'json' => $response->json()
        ]);

        if (!$response->successful()) {
            throw new \Exception('Failed to delete test case: ' . $response->body());
        }

        return $response->json();
    }
} 