@extends('layouts.app')

@section('title', 'Create New Problem')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="p-6">
        <div class="mb-6">
            <a href="{{ route('manage.problems.index') }}" 
                class="mr-4 text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">CREATE NEW PROBLEM</h1>
        </div>

        <form method="POST" action="{{ route('manage.problems.store') }}" class="space-y-6">
            @csrf
            
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Problem Name <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" 
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
                    placeholder="Enter problem name..." required>
                @error('name')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="difficulty" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Difficulty <span class="text-red-500">*</span>
                </label>
                <select name="difficulty" id="difficulty" 
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
                    required>
                    <option value="">Select difficulty...</option>
                    <option value="easy" {{ old('difficulty') === 'easy' ? 'selected' : '' }}>Easy</option>
                    <option value="medium" {{ old('difficulty') === 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="hard" {{ old('difficulty') === 'hard' ? 'selected' : '' }}>Hard</option>
                </select>
                @error('difficulty')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <div class="flex items-center">
                    <input type="checkbox" name="is_public" id="is_public" value="1" 
                        {{ old('is_public') ? 'checked' : '' }}
                        class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                    <label for="is_public" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                        Make this problem public
                    </label>
                </div>
                @error('is_public')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="time_limit" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Time Limit (seconds) <span class="text-red-500">*</span>
                </label>
                <input type="number" name="time_limit" id="time_limit" min="1" max="60" 
                    value="{{ old('time_limit', 5) }}" 
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
                    required>
                @error('time_limit')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="memory_limit" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Memory Limit (MB) <span class="text-red-500">*</span>
                </label>
                <input type="number" name="memory_limit" id="memory_limit" min="1" max="512" 
                    value="{{ old('memory_limit', 256) }}" 
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
                    required>
                @error('memory_limit')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Problem Content (Markdown) <span class="text-red-500">*</span>
                </label>
                <textarea name="content" id="content" rows="12" 
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white font-mono text-sm"
                    placeholder="Write your problem description in Markdown format..." required>{{ old('content', '# Problem Description

Write your problem description here using Markdown syntax.

## Input

Describe the input format.

## Output

Describe the output format.

## Sample Input

```
1 2
```

## Sample Output

```
3
```

## Constraints

- Describe any constraints here') }}</textarea>
                @error('content')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    You can use Markdown syntax and LaTeX math expressions.
                </p>
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('manage.problems.index') }}" 
                    class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition duration-200">
                    Cancel
                </a>
                <button type="submit" 
                    class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition duration-200">
                    Create Problem
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('content').addEventListener('input', function () {
    this.style.height = 'auto';
    this.style.height = this.scrollHeight + 'px';
});
</script>
@endsection 