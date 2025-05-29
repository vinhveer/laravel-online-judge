@extends('layouts.app')

@section('title', 'Submit Solution - ' . $problem->name)

@section('styles')
<style>
    #editor {
        height: 500px;
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
    }
    .dark #editor {
        border-color: #374151;
    }
</style>
@endsection

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-5xl mx-auto">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h1 class="text-xl font-semibold text-gray-900 dark:text-white">
                    Submit Solution for {{ $problem->name }}
                </h1>
            </div>

            <!-- Content -->
            <div class="p-6">
                <form action="{{ route('submissions.store', $problem) }}" method="POST" id="submitForm">
                    @csrf
                    
                    <div class="mb-6">
                        <div class="flex items-center gap-4">
                            <label for="language" class="whitespace-nowrap text-sm font-medium text-gray-700 dark:text-gray-300">
                                Programming Language
                            </label>
                            <div class="relative flex-1 max-w-xs" x-data="{ open: false }">
                                <button type="button" 
                                        @click="open = !open"
                                        class="w-full flex items-center justify-between px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600">
                                    <span id="selectedLanguage">C++</span>
                                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <div x-show="open" 
                                     @click.away="open = false"
                                     x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="transform opacity-0 scale-95"
                                     x-transition:enter-end="transform opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="transform opacity-100 scale-100"
                                     x-transition:leave-end="transform opacity-0 scale-95"
                                     class="absolute z-10 mt-1 w-full rounded-md bg-white dark:bg-gray-700 shadow-lg">
                                    <ul class="py-1">
                                        <li>
                                            <button type="button" 
                                                    @click="open = false; document.getElementById('selectedLanguage').textContent = 'C++'; document.getElementById('language').value = 'cpp'; monaco.editor.setModelLanguage(editor.getModel(), 'cpp');"
                                                    class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600">
                                                C++
                                            </button>
                                        </li>
                                        <li>
                                            <button type="button"
                                                    @click="open = false; document.getElementById('selectedLanguage').textContent = 'Java'; document.getElementById('language').value = 'java'; monaco.editor.setModelLanguage(editor.getModel(), 'java');"
                                                    class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600">
                                                Java
                                            </button>
                                        </li>
                                        <li>
                                            <button type="button"
                                                    @click="open = false; document.getElementById('selectedLanguage').textContent = 'Python'; document.getElementById('language').value = 'python'; monaco.editor.setModelLanguage(editor.getModel(), 'python');"
                                                    class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600">
                                                Python
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="language" id="language" value="cpp">
                    </div>

                    <div class="mb-6">
                        <div class="flex items-center gap-4 mb-2">
                            <label for="code" class="whitespace-nowrap text-sm font-medium text-gray-700 dark:text-gray-300">
                                Source Code
                            </label>
                            <input type="file" 
                                   id="fileInput" 
                                   accept=".cpp,.java,.py"
                                   class="flex-1 text-sm text-gray-500
                                          file:mr-4 file:py-2 file:px-4
                                          file:rounded-md file:border-0
                                          file:text-sm file:font-medium
                                          file:bg-primary-50 file:text-primary-700
                                          hover:file:bg-primary-100
                                          dark:file:bg-gray-700 dark:file:text-gray-200
                                          dark:hover:file:bg-gray-600">
                        </div>
                        <div id="editor"></div>
                        <input type="hidden" name="code" id="code">
                    </div>

                    <div class="flex justify-end space-x-4">
                        <a href="{{ route('problems.show', $problem) }}" 
                           class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600">
                            Cancel
                        </a>
                        <button type="submit"
                                class="px-4 py-2 text-sm font-medium text-white bg-primary-600 border border-transparent rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            Submit Solution
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.45.0/min/vs/loader.min.js"></script>
<script>
    require.config({ paths: { 'vs': 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.45.0/min/vs' }});
    require(['vs/editor/editor.main'], function() {
        const codeInput = document.getElementById('code');
        const fileInput = document.getElementById('fileInput');
        let editor;
        
        // Initialize Monaco Editor
        editor = monaco.editor.create(document.getElementById('editor'), {
            value: '',
            language: 'cpp',
            theme: 'vs-dark',
            automaticLayout: true,
            minimap: {
                enabled: false
            },
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

        // Handle file upload
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function(e) {
                editor.setValue(e.target.result);
            };
            reader.readAsText(file);
        });

        // Update hidden input before form submit
        document.getElementById('submitForm').addEventListener('submit', function() {
            codeInput.value = editor.getValue();
        });

        // Set initial language
        monaco.editor.setModelLanguage(editor.getModel(), 'cpp');
    });
</script>
@endsection 