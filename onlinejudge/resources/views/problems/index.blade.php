@extends('layouts.app')

@section('title', 'Problems List')

@section('content')
<div class="container-fluid px-5 mx-auto py-8">
    <h2 class="text-xl font-bold mb-6">Problems Lists</h2>
    <div class="flex">
        <div class="w-3/4 pr-4">
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white dark:bg-gray-800 rounded shadow text-sm">
                    <thead>
                        <tr>
                            <th class="px-4 py-2 text-left">Name</th>
                            <th class="px-4 py-2 text-left">Difficulty</th>
                            <th class="px-4 py-2 text-left">Time Limit (ms)</th>
                            <th class="px-4 py-2 text-left">Memory Limit (MB)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($problems as $problem)
                        <tr class="border-t dark:border-gray-700">
                            <td class="px-4 py-2 font-medium text-left">
                                <a href="{{ url('/problems/'.$problem->id) }}" class="text-indigo-600 hover:underline">{{ $problem->name }}</a>
                            </td>
                            <td class="px-4 py-2 text-left">{{ ucfirst($problem->difficulty) }}</td>
                            <td class="px-4 py-2 text-left">{{ $problem->time_limit }}</td>
                            <td class="px-4 py-2 text-left">{{ $problem->memory_limit }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-4">No problems found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $problems->appends(request()->query())->links() }}
            </div>
        </div>
        <div class="w-1/4">
            <div class="bg-white dark:bg-gray-800 rounded shadow p-4">
                <h3 class="text-lg font-bold mb-4">Search and Filter</h3>
                <form action="{{ url('/problems') }}" method="GET">
                    <div class="mb-4">
                        <label for="search" class="block text-sm font-medium mb-1">Enter here to search</label>
                        <input type="text" id="search" name="search" class="w-full px-3 py-2 border rounded dark:bg-gray-700" placeholder="Nhập tên bài..." value="{{ request('search') }}">
                    </div>
                    <div class="mb-4">
                        <label for="difficulty" class="block text-sm font-medium mb-1">Difficulty</label>
                        <select id="difficulty" name="difficulty" class="w-full px-3 py-2 border rounded dark:bg-gray-700">
                            <option value="">All</option>
                            <option value="easy" {{ request('difficulty') == 'easy' ? 'selected' : '' }}>Easy</option>
                            <option value="medium" {{ request('difficulty') == 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="hard" {{ request('difficulty') == 'hard' ? 'selected' : '' }}>Hard</option>
                        </select>
                    </div>
                    <button type="submit" class="w-full bg-indigo-600 text-white py-2 rounded hover:bg-indigo-700">Tìm kiếm</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 