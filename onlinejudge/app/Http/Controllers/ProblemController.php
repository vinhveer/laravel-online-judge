<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Problem;

class ProblemController extends Controller
{
    public function index(Request $request)
    {
        $query = Problem::where('is_public', true);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('difficulty')) {
            $query->where('difficulty', $request->difficulty);
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
