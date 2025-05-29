@extends('layouts.app')

@section('title', 'Manage Problem Description - ' . $problem->name)

@section('styles')
<style>
    #editor {
        height: calc(100vh - 200px);
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
    }
    .dark #editor {
        border-color: #374151;
    }
    .preview {
        height: calc(100vh - 200px);
        overflow-y: auto;
    }
</style>
@endsection

@section('content')
<div class="container mx-auto py-10">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-3 flex justify-between items-center">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">
                Manage Problem Description: {{ $problem->name }}
            </h1>
            <button id="saveContent" 
                class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-primary-600 border border-transparent rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors duration-200">
                Save Changes
            </button>
        </div>

        <!-- Main Content -->
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="grid grid-cols-2 gap-6 p-6">
                <!-- Editor -->
                <div>
                    <div class="mb-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Markdown Editor</label>
                    </div>
                    <div id="editor"></div>
                </div>

                <!-- Preview -->
                <div>
                    <div class="mb-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Preview</label>
                    </div>
                    <div id="preview" class="preview markdown-body prose dark:prose-invert max-w-none bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700"></div>
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

        // Auto preview on change
        editor.onDidChangeModelContent(() => {
            const content = editor.getValue();
            document.getElementById('preview').innerHTML = marked.parse(content);
        });

        // Save content
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

        // Initial preview
        document.getElementById('preview').innerHTML = marked.parse(editor.getValue());
    });
</script>
@endsection 