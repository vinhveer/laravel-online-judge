<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Problem;

class ProblemController extends Controller
{
    public function index(Request $request)
    {
        $query = Problem::where('is_public', true);

        // Search by name
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
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

        $problems = $query->orderByDesc('created_at')->paginate(10);
        return view('problems.index', compact('problems'));
    }

    public function show(Problem $problem)
    {
        if (!$problem->is_public) {
            abort(404);
        }
        
        return view('problems.show', compact('problem'));
    }
}
