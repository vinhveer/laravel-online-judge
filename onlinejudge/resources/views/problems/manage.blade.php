@extends('layouts.app')

@section('title', 'Manage Problem - ' . $problem->name)

@section('styles')
<style>
    #editor {
        height: 600px;
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
    }
    .dark #editor {
        border-color: #374151;
    }
    .preview {
        height: 600px;
        overflow-y: auto;
    }
</style>
@endsection

@section('content')
<div class="container mx-auto py-10">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-3">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">
                Manage Problem: {{ $problem->name }}
            </h1>
        </div>

        <!-- Tabs -->
        <div class="border-b border-gray-200 dark:border-gray-700 mb-8">
            <nav class="flex -mb-px space-x-8" aria-label="Tabs">
                <button class="tab-button active px-1 py-4 text-sm font-medium border-b-2 border-primary-500 text-primary-600 dark:text-primary-400" data-tab="description">
                    Description
                </button>
                <button class="tab-button px-1 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300" data-tab="content">
                    Edit Content
                </button>
                <button class="tab-button px-1 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300" data-tab="testcases">
                    Test Cases
                </button>
            </nav>
        </div>

        <!-- Tab Content -->
        <div>
            <!-- Description Tab -->
            <div class="tab-content active" id="description-tab">
                <form id="descriptionForm" class="space-y-6 max-w-2xl">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Problem Name</label>
                        <input type="text" name="name" id="name" value="{{ $problem->name }}" 
                            class="block w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 shadow-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm">
                    </div>

                    <div>
                        <label for="difficulty" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Difficulty</label>
                        <div class="relative">
                            <select name="difficulty" id="difficulty" 
                                class="block w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 shadow-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm appearance-none bg-no-repeat bg-[length:1.5em_1.5em] bg-[right_0.5rem_center] bg-[url('data:image/svg+xml,%3csvg xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 20 20\'%3e%3cpath stroke=\'%236b7280\' stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'1.5\' d=\'M6 8l4 4 4-4\'/%3e%3c/svg%3e')]">
                                <option value="easy" {{ $problem->difficulty === 'easy' ? 'selected' : '' }}>Easy</option>
                                <option value="medium" {{ $problem->difficulty === 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="hard" {{ $problem->difficulty === 'hard' ? 'selected' : '' }}>Hard</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700 dark:text-gray-300">
                                <svg class="h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="time_limit" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Time Limit (seconds)</label>
                            <input type="number" name="time_limit" id="time_limit" value="{{ $problem->time_limit }}" 
                                class="block w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 shadow-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm">
                        </div>
                        <div>
                            <label for="memory_limit" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Memory Limit (MB)</label>
                            <input type="number" name="memory_limit" id="memory_limit" value="{{ $problem->memory_limit }}" 
                                class="block w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 shadow-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm">
                        </div>
                    </div>

                    <div class="flex items-center">
                        <div class="relative flex items-start">
                            <div class="flex h-6 items-center">
                                <input type="checkbox" name="is_public" id="is_public" {{ $problem->is_public ? 'checked' : '' }} 
                                    class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:checked:bg-primary-600 dark:checked:border-primary-600">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="is_public" class="font-medium text-gray-700 dark:text-gray-300">Make this problem public</label>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" 
                            class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-primary-600 border border-transparent rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors duration-200">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>

            <!-- Content Tab -->
            <div class="tab-content hidden" id="content-tab">
                <div class="grid grid-cols-2 gap-8">
                    <div>
                        <div class="mb-2 flex justify-between items-center">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Markdown Editor</label>
                            <button id="previewButton" class="text-sm text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 transition-colors duration-200">
                                Preview
                            </button>
                        </div>
                        <div id="editor"></div>
                    </div>
                    <div>
                        <div class="mb-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Preview</label>
                        </div>
                        <div id="preview" class="preview markdown-body prose dark:prose-invert max-w-none bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700"></div>
                    </div>
                </div>
                <div class="mt-4 flex justify-end">
                    <button id="saveContent" 
                        class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-primary-600 border border-transparent rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors duration-200">
                        Save Changes
                    </button>
                </div>
            </div>

            <!-- Test Cases Tab -->
            <div class="tab-content hidden" id="testcases-tab">
                <div class="space-y-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Test Cases</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Upload a zip file containing .in and .out files</p>
                        </div>
                        <div class="flex space-x-4">
                            <button id="downloadTestCases" 
                                class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600 transition-colors duration-200">
                                Download All
                            </button>
                            <label class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-primary-600 border border-transparent rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 cursor-pointer transition-colors duration-200">
                                Upload Zip
                                <input type="file" id="testCasesFile" class="hidden" accept=".zip">
                            </label>
                        </div>
                    </div>

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
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.45.0/min/vs/loader.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/marked@12.0.0/marked.min.js"></script>
<script>
    require.config({ paths: { 'vs': 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.45.0/min/vs' }});
    require(['vs/editor/editor.main'], function() {
        // Initialize Monaco Editor
        const editor = monaco.editor.create(document.getElementById('editor'), {
            value: `{{ $problem->content }}`,
            language: 'markdown',
            theme: 'vs-dark',
            automaticLayout: true,
            minimap: { enabled: false },
            fontSize: 14,
            tabSize: 4,
            scrollBeyondLastLine: false,
            lineNumbers: 'on',
            roundedSelection: false,
            scrollbar: {
                vertical: 'visible',
                horizontal: 'visible',
                useShadows: false,
                verticalScrollbarSize: 10,
                horizontalScrollbarSize: 10
            }
        });

        // Tab switching
        document.querySelectorAll('.tab-button').forEach(button => {
            button.addEventListener('click', () => {
                // Update buttons
                document.querySelectorAll('.tab-button').forEach(btn => {
                    btn.classList.remove('active', 'border-primary-500', 'text-primary-600', 'dark:text-primary-400');
                    btn.classList.add('border-transparent', 'text-gray-500', 'dark:text-gray-400');
                });
                button.classList.add('active', 'border-primary-500', 'text-primary-600', 'dark:text-primary-400');
                button.classList.remove('border-transparent', 'text-gray-500', 'dark:text-gray-400');

                // Update content
                document.querySelectorAll('.tab-content').forEach(content => {
                    content.classList.add('hidden');
                });
                document.getElementById(`${button.dataset.tab}-tab`).classList.remove('hidden');
            });
        });

        // Description form handling
        document.getElementById('descriptionForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            formData.append('is_public', document.getElementById('is_public').checked);

            try {
                const response = await fetch(`{{ route('problems.update-description', $problem) }}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(Object.fromEntries(formData))
                });

                const data = await response.json();
                if (data.status === 'success') {
                    alert('Problem description updated successfully');
                }
            } catch (error) {
                alert('Failed to update problem description');
            }
        });

        // Content preview and save
        document.getElementById('previewButton').addEventListener('click', () => {
            const content = editor.getValue();
            document.getElementById('preview').innerHTML = marked.parse(content);
        });

        document.getElementById('saveContent').addEventListener('click', async () => {
            try {
                const response = await fetch(`{{ route('problems.update-content', $problem) }}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        content: editor.getValue()
                    })
                });

                const data = await response.json();
                if (data.status === 'success') {
                    alert('Problem content updated successfully');
                }
            } catch (error) {
                alert('Failed to update problem content');
            }
        });

        // Test cases handling
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

        document.getElementById('testCasesFile').addEventListener('change', async (e) => {
            const file = e.target.files[0];
            if (!file) return;

            const formData = new FormData();
            formData.append('file', file);

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
                    alert(`Successfully imported ${data.count} test cases`);
                    loadTestCases();
                } else {
                    alert('Failed to import test cases');
                }
            } catch (error) {
                alert('Failed to import test cases');
            }
        });

        document.getElementById('downloadTestCases').addEventListener('click', () => {
            window.location.href = `{{ route('problems.download-testcases', $problem) }}`;
        });

        // Load test cases when tab is opened
        document.querySelector('[data-tab="testcases"]').addEventListener('click', loadTestCases);

        // Initial preview
        document.getElementById('preview').innerHTML = marked.parse(editor.getValue());
    });
</script>
@endsection 