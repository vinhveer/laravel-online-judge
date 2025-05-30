@extends('layouts.app')

@section('title', 'Edit Content: ' . $problem->name)

@section('styles')
    <!-- KaTeX CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.css"
        integrity="sha384-n8MVd4RsNIU0tAv4ct0nTaAbDJwPJzDEaqSD1odI+WdtXRGWt2kTvGFasHpSy3SV" crossorigin="anonymous">

    <!-- GitHub Markdown CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/github-markdown-css@5.4.0/github-markdown.min.css">
    <style>
        /* Preview Container */
        #preview-container {
            height: 70vh;
            overflow-y: auto;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 1.5rem;
            background: #f9fafb;
        }

        .dark #preview-container {
            border-color: #374151;
            background: #1f2937;
        }

        /* Markdown styles */
        .markdown-body {
            background: transparent;
            color: inherit !important;
        }

        body:not(.dark) .markdown-body {
            color: #1a202c;
        }

        .dark .markdown-body {
            color: #f7fafc;
        }

        .markdown-body ul {
            list-style-type: disc;
            margin-left: 1.25rem;
        }

        .markdown-body ol {
            list-style-type: decimal;
            margin-left: 1.25rem;
        }

        .markdown-body pre {
            background: transparent;
            padding: 1.5em;
            border-radius: 8px;
            overflow-x: auto;
            margin: 1.5em 0;
            border: 1px solid #e5e7eb;
        }

        .dark .markdown-body pre {
            border-color: #374151;
        }

        .markdown-body code {
            padding: 0.2em 0.4em;
            border-radius: 4px;
            font-size: 0.9em;
            font-family: 'SF Mono', Monaco, 'Cascadia Code', 'Roboto Mono', Consolas, monospace;
        }

        .markdown-body code {
            color: #1f2937;
        }

        .dark .markdown-body code {
            color: #d1d5db;
        }

        /* Save indicator */
        .save-indicator {
            transition: all 0.3s ease;
        }

        .save-indicator.saving {
            color: #f59e0b;
        }

        .save-indicator.saved {
            color: #10b981;
        }

        .save-indicator.error {
            color: #ef4444;
        }
    </style>
@endsection

@section('content')
<div class="container-fluid px-5 mx-auto py-8">
    @include('partials.problem-management-tabs', ['problem' => $problem, 'activeTab' => 'content'])

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Edit Problem Content</h2>
            <div class="flex items-center space-x-4">
                <span id="save-indicator" class="save-indicator text-sm font-medium">
                    Ready to edit
                </span>
                <button id="save-button" onclick="saveContent()" 
                    class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition duration-200">
                    Save Content
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Editor -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">
                    Markdown Editor
                </h3>
                <textarea id="editor" class="w-full h-[70vh] px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white font-mono text-sm">{{ $problem->content }}</textarea>
            </div>

            <!-- Preview -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">
                    Preview
                </h3>
                <div id="preview-container">
                    <div class="markdown-body prose dark:prose-invert max-w-none" id="markdown-preview">
                        <div class="text-gray-500 dark:text-gray-400 italic">
                            Start typing to see the preview...
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-6 text-sm text-gray-600 dark:text-gray-400">
            <p><strong>Tip:</strong> You can use Markdown syntax and LaTeX math expressions.</p>
            <p>Math: Use <code>$x^2$</code> for inline math or <code>$$x^2$$</code> for display math.</p>
            <p>Auto-save occurs every 30 seconds, or press Ctrl+S to save manually.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <!-- Marked.js -->
    <script src="https://cdn.jsdelivr.net/npm/marked@12.0.0/marked.min.js"></script>
    
    <!-- KaTeX -->
    <script src="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.js"
        integrity="sha384-XjKyOOlGwcjNTAIQHIpgOno0Hl1YQqzUOEleOLALmuqehneUG+vnGctmUb0ZY0l8" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/contrib/auto-render.min.js"
        integrity="sha384-+VBxd3r6XgURycqtZ117nYw44OOcIax56Z4dCRWbxyPt0Koah1uHoK0o4+/RRE05" crossorigin="anonymous">
    </script>

    <script>
        const editor = document.getElementById('editor');
        const saveIndicator = document.getElementById('save-indicator');
        let saveTimer;
        let originalContent = editor.value;

        // Configure Marked
        marked.setOptions({
            highlight: function(code, lang) {
                if (window.hljs && lang && hljs.getLanguage(lang)) {
                    try {
                        return hljs.highlight(code, { language: lang }).value;
                    } catch (_) {}
                }
                return window.hljs ? hljs.highlightAuto(code).value : code;
            },
            gfm: true,
            breaks: true,
        });

        // Update preview on content change
        editor.addEventListener('input', function() {
            updatePreview();
            resetSaveTimer();
        });

        // Handle keyboard shortcuts
        editor.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                e.preventDefault();
                saveContent();
            }
        });

        function updatePreview() {
            const content = editor.value;
            const previewElement = document.getElementById('markdown-preview');
            
            try {
                if (content.trim() === '') {
                    previewElement.innerHTML = '<div class="text-gray-500 dark:text-gray-400 italic">Start typing to see the preview...</div>';
                    return;
                }

                const html = marked.parse(content);
                previewElement.innerHTML = html;

                // Render math expressions
                if (window.renderMathInElement) {
                    renderMathInElement(previewElement, {
                        delimiters: [
                            {left: "$$", right: "$$", display: true},
                            {left: "$", right: "$", display: false},
                            {left: "\\(", right: "\\)", display: false},
                            {left: "\\[", right: "\\]", display: true}
                        ],
                        throwOnError: false,
                        errorColor: "#cc0000"
                    });
                }

                // Highlight code blocks
                if (window.hljs) {
                    previewElement.querySelectorAll('pre code').forEach((block) => {
                        hljs.highlightElement(block);
                    });
                }
            } catch (e) {
                previewElement.innerHTML = `
                    <div class="text-red-600 p-4 border border-red-300 rounded">
                        Error rendering markdown: ${e.message}
                    </div>`;
            }
        }

        function saveContent() {
            const content = editor.value;
            const saveButton = document.getElementById('save-button');
            
            // Update UI
            saveIndicator.textContent = 'Saving...';
            saveIndicator.className = 'save-indicator saving text-sm font-medium';
            saveButton.disabled = true;
            saveButton.textContent = 'Saving...';

            // Send AJAX request
            fetch('{{ route("manage.problems.content.update", $problem->id) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    content: content
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    saveIndicator.textContent = 'Saved';
                    saveIndicator.className = 'save-indicator saved text-sm font-medium';
                    // Update originalContent after successful save
                    originalContent = content;
                    
                    setTimeout(() => {
                        saveIndicator.textContent = 'Ready to edit';
                        saveIndicator.className = 'save-indicator text-sm font-medium';
                    }, 2000);
                } else {
                    throw new Error(data.message || 'Failed to save');
                }
            })
            .catch(error => {
                console.error('Save error:', error);
                saveIndicator.textContent = 'Error saving';
                saveIndicator.className = 'save-indicator error text-sm font-medium';
                
                setTimeout(() => {
                    saveIndicator.textContent = 'Ready to edit';
                    saveIndicator.className = 'save-indicator text-sm font-medium';
                }, 3000);
            })
            .finally(() => {
                saveButton.disabled = false;
                saveButton.textContent = 'Save Content';
            });
        }

        function resetSaveTimer() {
            clearTimeout(saveTimer);
            saveTimer = setTimeout(() => {
                if (editor.value !== originalContent) {
                    saveContent();
                }
            }, 30000); // Auto-save every 30 seconds
        }

        // Initial preview update
        updatePreview();

        // Clean up on page unload
        window.addEventListener('beforeunload', function(e) {
            if (editor.value !== originalContent) {
                e.preventDefault();
                e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
            }
        });
    </script>
@endsection 