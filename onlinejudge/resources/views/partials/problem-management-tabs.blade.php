@props(['problem', 'activeTab' => 'information'])

<div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 mb-6">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
            {{ $problem->name }}
        </h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">
            Problem ID: {{ $problem->id }}
        </p>
    </div>

    <div class="border-b border-gray-200 dark:border-gray-700">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            <!-- Information Tab -->
            <a href="{{ route('manage.problems.show', ['problem' => $problem->id]) }}"
                class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm flex items-center {{ $activeTab === 'information' ? 'border-primary-500 text-primary-600 dark:text-primary-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
                <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Information</span>
            </a>

            <!-- Content Tab -->
            <a href="{{ route('manage.problems.content', ['problem' => $problem->id]) }}"
                class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm flex items-center {{ $activeTab === 'content' ? 'border-primary-500 text-primary-600 dark:text-primary-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
                <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span>Content</span>
            </a>

            <!-- Test Cases Tab -->
            <a href="{{ route('manage.problems.testcases', ['problem' => $problem->id]) }}"
                class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm flex items-center {{ $activeTab === 'testcases' ? 'border-primary-500 text-primary-600 dark:text-primary-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
                <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                </svg>
                <span>Test Cases</span>
            </a>
        </nav>
    </div>
</div> 