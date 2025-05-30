@extends('layouts.app')

@section('title', 'Manage Problem: ' . $problem->name)

@section('content')
<div class="container px-5 mx-auto py-8">
    @include('partials.problem-management-tabs', ['problem' => $problem, 'activeTab' => 'information'])

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
        <div class="flex justify-between items-start mb-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Problem Information</h2>
            <a href="{{ route('manage.problems.edit', $problem->id) }}" 
                class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition duration-200">
                <span id="edit-button-text">Edit</span>
            </a>
        </div>

        <!-- Display Mode -->
        <div id="display-mode">
            <table class="w-full">
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <tr>
                        <td class="py-3 px-4 text-sm font-medium text-gray-700 dark:text-gray-300 w-1/3">Problem Name</td>
                        <td class="py-3 px-4 text-sm text-gray-900 dark:text-white">{{ $problem->name }}</td>
                    </tr>
                    <tr>
                        <td class="py-3 px-4 text-sm font-medium text-gray-700 dark:text-gray-300">Difficulty</td>
                        <td class="py-3 px-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $problem->difficulty === 'easy' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 
                                   ($problem->difficulty === 'medium' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : 
                                   'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200') }}">
                                {{ ucfirst($problem->difficulty) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="py-3 px-4 text-sm font-medium text-gray-700 dark:text-gray-300">Time Limit</td>
                        <td class="py-3 px-4 text-sm text-gray-900 dark:text-white">{{ $problem->time_limit }} seconds</td>
                    </tr>
                    <tr>
                        <td class="py-3 px-4 text-sm font-medium text-gray-700 dark:text-gray-300">Memory Limit</td>
                        <td class="py-3 px-4 text-sm text-gray-900 dark:text-white">{{ $problem->memory_limit }} MB</td>
                    </tr>
                    <tr>
                        <td class="py-3 px-4 text-sm font-medium text-gray-700 dark:text-gray-300">Public Status</td>
                        <td class="py-3 px-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $problem->is_public ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 
                                   'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200' }}">
                                {{ $problem->is_public ? 'Public' : 'Private' }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="py-3 px-4 text-sm font-medium text-gray-700 dark:text-gray-300">Created At</td>
                        <td class="py-3 px-4 text-sm text-gray-900 dark:text-white">
                            {{ $problem->created_at ? \Carbon\Carbon::parse($problem->created_at)->format('M d, Y H:i') : 'N/A' }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function toggleEditMode() {
    const displayMode = document.getElementById('display-mode');
    const editMode = document.getElementById('edit-mode');
    const buttonText = document.getElementById('edit-button-text');
    
    if (displayMode.style.display === 'none') {
        displayMode.style.display = 'block';
        editMode.style.display = 'none';
        buttonText.textContent = 'Edit';
    } else {
        displayMode.style.display = 'none';
        editMode.style.display = 'block';
        buttonText.textContent = 'Cancel';
    }
}
</script>
@endsection 