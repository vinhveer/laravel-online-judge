<nav class="flex items-center">
    <div class="flex items-center">
        <a href="/" class="mx-2 text-sm hover:text-indigo-600 dark:hover:text-indigo-400">Home</a>
        <a href="/problems" class="mx-2 text-sm hover:text-indigo-600 dark:hover:text-indigo-400">Problems</a>
        <a href="/submissions" class="mx-2 text-sm hover:text-indigo-600 dark:hover:text-indigo-400">Submissions</a>
        @if(Auth::check() && Auth::user()->is_admin)
            <a href="/account" class="mx-2 text-sm hover:text-indigo-600 dark:hover:text-indigo-400">Account</a>
        @endif
    </div>
</nav>