<?php

namespace App\Http\Controllers;

use App\Models\Problem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ManageProblemController extends Controller
{
    public function edit(Problem $problem, Request $request)
    {
        $tab = $request->get('tab', 'description');
        
        switch ($tab) {
            case 'description':
                return view('problems.description.manage', compact('problem'));
            case 'testcase':
                return view('problems.testcase.manage', compact('problem'));
            default:
                return view('problems.manage', compact('problem'));
        }
    }

    public function updateDescription(Request $request, Problem $problem)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'difficulty' => 'required|in:easy,medium,hard',
            'time_limit' => 'required|integer|min:1',
            'memory_limit' => 'required|integer|min:1',
            'is_public' => 'boolean'
        ]);

        $problem->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Problem description updated successfully'
        ]);
    }

    public function updateContent(Request $request, Problem $problem)
    {
        $validated = $request->validate([
            'content' => 'required|string'
        ]);

        $problem->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Problem content updated successfully'
        ]);
    }

    public function uploadTestCases(Request $request, Problem $problem)
    {
        $request->validate([
            'file' => 'required|file|mimes:zip'
        ]);

        $file = $request->file('file');
        $tempPath = storage_path('app/temp/' . Str::random());
        mkdir($tempPath, 0755, true);

        // Extract zip file
        $zip = new \ZipArchive;
        if ($zip->open($file->getPathname()) === TRUE) {
            $zip->extractTo($tempPath);
            $zip->close();

            // Process test cases
            $count = 0;
            $files = glob($tempPath . '/*');
            foreach ($files as $file) {
                if (preg_match('/^(\d+)\.in$/', basename($file), $matches)) {
                    $testNumber = $matches[1];
                    $input = file_get_contents($file);
                    $outputFile = $tempPath . '/' . $testNumber . '.out';
                    
                    if (file_exists($outputFile)) {
                        $output = file_get_contents($outputFile);
                        
                        $problem->test_cases()->create([
                            'input' => $input,
                            'expected_output' => $output,
                            'is_sample' => false
                        ]);
                        
                        $count++;
                    }
                }
            }

            // Cleanup
            Storage::deleteDirectory('temp/' . basename($tempPath));

            return response()->json([
                'status' => 'success',
                'message' => 'Test cases imported successfully',
                'count' => $count
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Failed to extract zip file'
        ], 400);
    }

    public function listTestCases(Problem $problem)
    {
        $testCases = $problem->test_cases()
            ->select('id', 'input', 'expected_output')
            ->get()
            ->mapWithKeys(function ($testCase) {
                return [$testCase->id => [
                    'in' => $testCase->input,
                    'out' => $testCase->expected_output
                ]];
            });

        return response()->json($testCases);
    }

    public function downloadTestCases(Problem $problem)
    {
        $zipPath = storage_path('app/temp/' . Str::random() . '.zip');
        $zip = new \ZipArchive;
        
        if ($zip->open($zipPath, \ZipArchive::CREATE) === TRUE) {
            foreach ($problem->test_cases as $testCase) {
                $zip->addFromString($testCase->id . '.in', $testCase->input);
                $zip->addFromString($testCase->id . '.out', $testCase->expected_output);
            }
            $zip->close();

            return response()->download($zipPath, 'testcases.zip')->deleteFileAfterSend();
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Failed to create zip file'
        ], 500);
    }
}
