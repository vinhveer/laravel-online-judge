@extends('layouts.app')

@section('title', $problem->name)

@section('styles')
    <!-- KaTeX CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.css"
        integrity="sha384-n8MVd4RsNIU0tAv4ct0nTaAbDJwPJzDEaqSD1odI+WdtXRGWt2kTvGFasHpSy3SV" crossorigin="anonymous">

    <!-- GitHub Markdown CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/github-markdown-css@5.4.0/github-markdown.min.css">
    <style>
        /* Xóa nền trắng của markdown-body */
        .markdown-body {
            background: transparent;
            color: inherit !important;
        }

        /* Light mode text */
        body:not(.dark) .markdown-body {
            color: #1a202c;
            /* text-gray-900 */
        }

        /* Dark mode text */
        .dark .markdown-body {
            color: #f7fafc;
            /* text-gray-100 */
        }

        /* Bullets & numbering */
        .markdown-body ul {
            list-style-type: disc;
            margin-left: 1.25rem;
        }

        .markdown-body ol {
            list-style-type: decimal;
            margin-left: 1.25rem;
        }

        /* Code blocks */
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

        /* Inline code */
        .markdown-body code {
            padding: 0.2em 0.4em;
            border-radius: 4px;
            font-size: 0.9em;
            font-family: 'SF Mono', Monaco, 'Cascadia Code', 'Roboto Mono', Consolas, monospace;
        }

        /* Light mode inline code */
        .markdown-body code {
            color: #1f2937;
        }

        /* Dark mode inline code */
        .dark .markdown-body code {
            color: #d1d5db;
        }


        /* Bullet list fix */
        .markdown-body ul {
            list-style-type: disc;
            padding-left: 1.5em;
        }

        .markdown-body ol {
            list-style-type: decimal;
            padding-left: 1.5em;
        }

        .markdown-body li::marker {
            color: currentColor;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid px-5 mx-auto py-8">
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Main Content -->
            <div class="lg:w-3/4">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                    <h1 class="text-2xl font-bold mb-6">{{ $problem->name }}</h1>

                    <!-- Markdown Content -->
                    <div class="markdown-body prose dark:prose-invert max-w-none" id="markdown-content">
                        <div class="math-loading rounded p-4 mb-4">
                            <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded mb-2"></div>
                            <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded mb-2 w-3/4"></div>
                            <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-1/2"></div>
                        </div>
                    </div>
                    <script type="text/plain" id="raw-markdown">{{ $problem->content }}</script>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:w-1/4">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 space-y-4">
                    <div>
                        <h3 class="text-lg font-semibold mb-2">Problem Information</h3>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Difficulty:</span>
                                <span class="font-medium">{{ ucfirst($problem->difficulty) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Time Limit:</span>
                                <span class="font-medium">{{ $problem->time_limit }}s</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Memory Limit:</span>
                                <span class="font-medium">{{ $problem->memory_limit }}MB</span>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold mb-2">Statistics</h3>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Total Submissions:</span>
                                <span class="font-medium">{{ $problem->submissions_count ?? 0 }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Success Rate:</span>
                                <span class="font-medium">{{ $problem->success_rate ?? 0 }}%</span>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                        <a href="{{ route('submissions.create', $problem) }}"
                            class="w-full bg-primary-600 hover:bg-primary-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200 flex items-center justify-center">
                            Submit Solution
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- KaTeX -->
    <script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.js"
        integrity="sha384-XjKyOOlGwcjNTAIQHIpgOno0Hl1YQqzUOEleOLALmuqehneUG+vnGctmUb0ZY0l8" crossorigin="anonymous">
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/contrib/auto-render.min.js"
        integrity="sha384-+VBxd3r6XgURycqtZ117nYw44OOcIax56Z4dCRWbxyPt0Koah1uHoK0o4+/RRE05" crossorigin="anonymous">
    </script>

    <!-- Marked.js -->
    <script src="https://cdn.jsdelivr.net/npm/marked@12.0.0/marked.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            marked.setOptions({
                highlight: function(code, lang) {
                    // fallback khi chưa có hljs: sẽ tự động highlight Auto
                    if (window.hljs && lang && hljs.getLanguage(lang)) {
                        try {
                            return hljs.highlight(code, {
                                language: lang
                            }).value;
                        } catch (_) {}
                    }
                    return window.hljs ? hljs.highlightAuto(code).value : code;
                },
                gfm: true,
                breaks: true,
            });

            const raw = document.getElementById('raw-markdown').textContent;
            const container = document.getElementById('markdown-content');

            try {
                const html = marked.parse(raw);
                container.innerHTML = html;

                renderMathInElement(container, {
                    delimiters: [{
                            left: "$$",
                            right: "$$",
                            display: true
                        },
                        {
                            left: "$",
                            right: "$",
                            display: false
                        },
                        {
                            left: "\\(",
                            right: "\\)",
                            display: false
                        },
                        {
                            left: "\\[",
                            right: "\\]",
                            display: true
                        }
                    ],
                    throwOnError: false,
                    errorColor: "#cc0000"
                });

                // Highlight.js: đánh dấu tất cả code blocks
                if (window.hljs) {
                    hljs.highlightAll();
                }
            } catch (e) {
                container.innerHTML = `
                  <div class="text-red-600 p-4 border border-red-300 rounded">
                    Error rendering markdown: ${e.message}
                  </div>`;
            }
        });
    </script>
@endsection
