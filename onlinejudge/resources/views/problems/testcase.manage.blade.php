@extends('layouts.app')

@section('title', 'Manage Test Cases - ' . $problem->name)

@section('content')
<div class="container mx-auto py-10">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-3 flex justify-between items-center">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">
                Manage Test Cases: {{ $problem->name }}
            </h1>
            <div class="flex space-x-4">
                <button id="downloadTestCases" 
                    class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600 transition-colors duration-200">
                    Download All
                </button>
                <div class="relative">
                    <button type="button" id="uploadButton"
                        class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-primary-600 border border-transparent rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors duration-200">
                        Upload Test Cases
                    </button>
                    <div id="uploadMenu" class="hidden absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5">
                        <div class="py-1" role="menu" aria-orientation="vertical">
                            <label class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer" role="menuitem">
                                Add New
                                <input type="file" id="addTestCases" class="hidden" accept=".zip">
                            </label>
                            <label class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer" role="menuitem">
                                Replace All
                                <input type="file" id="replaceTestCases" class="hidden" accept=".zip">
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="p-6">
                <!-- Test Cases List -->
                <div id="testCasesList" class="space-y-4">
                    <div class="animate-pulse">
                        <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-1/4 mb-4"></div>
                        <div class="space-y-3">
                            <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded"></div>
                            <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded"></div>
                            <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Toggle upload menu
    document.getElementById('uploadButton').addEventListener('click', () => {
        const menu = document.getElementById('uploadMenu');
        menu.classList.toggle('hidden');
    });

    // Close menu when clicking outside
    document.addEventListener('click', (e) => {
        const menu = document.getElementById('uploadMenu');
        const button = document.getElementById('uploadButton');
        if (!menu.contains(e.target) && !button.contains(e.target)) {
            menu.classList.add('hidden');
        }
    });

    // Handle file uploads
    async function handleFileUpload(file, isReplace = false) {
        const formData = new FormData();
        formData.append('file', file);
        if (isReplace) {
            formData.append('replace', '1');
        }

        try {
            const response = await fetch(`{{ route('problems.upload-testcases', $problem) }}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            });

            const data = await response.json();
            if (data.status === 'success') {
                alert(`Successfully ${isReplace ? 'replaced' : 'added'} ${data.count} test cases`);
                loadTestCases();
            } else {
                alert('Failed to import test cases');
            }
        } catch (error) {
            alert('Failed to import test cases');
        }
    }

    document.getElementById('addTestCases').addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (file) {
            handleFileUpload(file, false);
        }
    });

    document.getElementById('replaceTestCases').addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (file) {
            if (confirm('Are you sure you want to replace all existing test cases?')) {
                handleFileUpload(file, true);
            }
        }
    });

    // Load test cases
    async function loadTestCases() {
        try {
            const response = await fetch(`{{ route('problems.list-testcases', $problem) }}`);
            const testCases = await response.json();
            
            const container = document.getElementById('testCasesList');
            container.innerHTML = '';
            
            Object.entries(testCases).forEach(([id, testCase]) => {
                const div = document.createElement('div');
                div.className = 'p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700';
                div.innerHTML = `
                    <div class="flex justify-between items-center mb-2">
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white">Test Case #${id}</h4>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Input</label>
                            <pre class="text-xs bg-gray-50 dark:bg-gray-900 p-2 rounded">${testCase.in}</pre>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Expected Output</label>
                            <pre class="text-xs bg-gray-50 dark:bg-gray-900 p-2 rounded">${testCase.out}</pre>
                        </div>
                    </div>
                `;
                container.appendChild(div);
            });
        } catch (error) {
            console.error('Failed to load test cases:', error);
        }
    }

    // Download test cases
    document.getElementById('downloadTestCases').addEventListener('click', () => {
        window.location.href = `{{ route('problems.download-testcases', $problem) }}`;
    });

    // Load test cases on page load
    loadTestCases();
</script>
@endsection 