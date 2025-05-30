<?php

namespace App\Http\Controllers;

use App\Models\Problem;
use App\Models\TestCase;
use App\Services\TestCaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ManageProblemController extends Controller
{
    protected $testCaseService;

    public function __construct(TestCaseService $testCaseService)
    {
        $this->testCaseService = $testCaseService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Problem::query();

        // Search by name
        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        // Filter by difficulty
        if ($request->filled('difficulty')) {
            $query->where('difficulty', $request->difficulty);
        }

        // Filter by time limit
        if ($request->filled('time_limit')) {
            $query->where('time_limit', $request->time_limit);
        }

        // Filter by memory limit
        if ($request->filled('memory_limit')) {
            $query->where('memory_limit', $request->memory_limit);
        }

        // Filter by status (public/private)
        if ($request->filled('status')) {
            $query->where('is_public', $request->status === 'public');
        }

        $problems = $query->latest('created_at')->paginate(10);
        return view('manage.problems.index', compact('problems'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('manage.problems.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'required|string',
            'difficulty' => 'required|in:easy,medium,hard',
            'time_limit' => 'required|integer|min:1|max:60',
            'memory_limit' => 'required|integer|min:1|max:512',
            'is_public' => 'boolean'
        ]);

        $validated['id'] = Str::uuid();
        $validated['is_public'] = $request->has('is_public');

        $problem = Problem::create($validated);

        return redirect()->route('manage.problems.index', $problem->id)
            ->with('success', 'Problem created successfully.');
    }

    /**
     * Display the specified resource (Information tab).
     */
    public function show(Problem $problem)
    {
        return view('manage.problems.show', compact('problem'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Problem $problem)
    {
        return view('manage.problems.edit', compact('problem'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Problem $problem)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'difficulty' => 'required|in:easy,medium,hard',
            'time_limit' => 'required|integer|min:1|max:60',
            'memory_limit' => 'required|integer|min:1|max:512',
            'is_public' => 'boolean'
        ]);

        $validated['is_public'] = $request->has('is_public');

        $problem->update($validated);

        return redirect()->route('manage.problems.show', $problem->id)
            ->with('success', 'Problem updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Problem $problem)
    {
        $problem->delete();

        return redirect()->route('manage.problems.index')
            ->with('success', 'Problem deleted successfully.');
    }

    /**
     * Show the content editing page.
     */
    public function content(Problem $problem)
    {
        return view('manage.problems.content', compact('problem'));
    }

    /**
     * Update the content of the problem.
     */
    public function updateContent(Request $request, Problem $problem)
    {
        $validated = $request->validate([
            'content' => 'required|string',
        ]);

        $problem->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Content updated successfully.'
        ]);
    }

    /**
     * Show the test cases page.
     */
    public function testcases(Problem $problem)
    {
        try {
            $testCases = $this->testCaseService->listTestCases($problem->id);
            return view('manage.problems.testcases', compact('problem', 'testCases'));
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to load test cases: ' . $e->getMessage());
        }
    }

    /**
     * Store test cases from a ZIP file.
     */
    public function storeTestCase(Request $request, Problem $problem)
    {
        $request->validate([
            'file' => 'required|file|mimes:zip'
        ]);

        try {
            $result = $this->testCaseService->uploadTestCases($problem->id, $request->file('file'));
            return back()->with('success', "Successfully imported {$result['count']} test cases");
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to upload test cases: ' . $e->getMessage());
        }
    }

    /**
     * Download all test cases.
     */
    public function downloadTestCases(Problem $problem)
    {
        try {
            $content = $this->testCaseService->downloadTestCases($problem->id);
            return response($content)
                ->header('Content-Type', 'application/zip')
                ->header('Content-Disposition', 'attachment; filename="testcases.zip"');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to download test cases: ' . $e->getMessage());
        }
    }

    /**
     * Delete all test cases.
     */
    public function destroyTestCase(Problem $problem)
    {
        try {
            Log::info('Attempting to delete all test cases from controller', [
                'problem_id' => $problem->id
            ]);

            $result = $this->testCaseService->deleteAllTestCases($problem->id);
            
            Log::info('Successfully deleted all test cases', [
                'problem_id' => $problem->id,
                'result' => $result
            ]);

            return back()->with('success', 'All test cases deleted successfully');
        } catch (\Exception $e) {
            Log::error('Failed to delete all test cases', [
                'problem_id' => $problem->id,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Failed to delete test cases: ' . $e->getMessage());
        }
    }

    /**
     * Delete a single test case.
     */
    public function destroySingleTestCase(Problem $problem, $testId)
    {
        try {
            Log::info('Attempting to delete single test case from controller', [
                'problem_id' => $problem->id,
                'test_id' => $testId
            ]);

            $result = $this->testCaseService->deleteTestCase($problem->id, $testId);
            
            Log::info('Successfully deleted single test case', [
                'problem_id' => $problem->id,
                'test_id' => $testId,
                'result' => $result
            ]);

            return back()->with('success', 'Test case deleted successfully');
        } catch (\Exception $e) {
            Log::error('Failed to delete single test case', [
                'problem_id' => $problem->id,
                'test_id' => $testId,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Failed to delete test case: ' . $e->getMessage());
        }
    }
}
