<?php

namespace App\Http\Controllers;

use App\Models\Problem;
use App\Models\Submission;
use Illuminate\Http\Request;

class SubmissionController extends Controller
{
    public function create(Problem $problem)
    {
        return view('submissions.create', compact('problem'));
    }

    public function store(Request $request, Problem $problem)
    {
        $validated = $request->validate([
            'code' => 'required|string',
            'language' => 'required|string|in:cpp,java,python',
        ]);

        $submission = $problem->submissions()->create([
            'user_id' => auth()->id(),
            'code' => $validated['code'],
            'language' => $validated['language'],
            'status' => 'pending',
        ]);

        // TODO: Add job to process submission

        return redirect()->route('submissions.show', $submission)
            ->with('success', 'Submission created successfully');
    }

    public function show(Submission $submission)
    {
        return view('submissions.show', compact('submission'));
    }
} 