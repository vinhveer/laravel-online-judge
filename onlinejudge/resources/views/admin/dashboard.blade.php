@extends('layouts.app')

@section('content')
<div class="container-fluid px-5 mx-auto py-8">
    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white mb-6">ADMINISTRATION DASHBOARD</h1>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Problem Management Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Problem Management</h2>
                <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
            </div>
            <p class="text-gray-600 mb-4">Manage problems, test cases, and submissions</p>
            <a href="/manage/problems" class="text-blue-500 hover:text-blue-700 font-medium">View Problems →</a>
        </div>

        <!-- Account Management Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Account Management</h2>
                <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </div>
            <p class="text-gray-600 mb-4">Manage user accounts and permissions</p>
            <a href="/manage/accounts" class="text-green-500 hover:text-green-700 font-medium">View Accounts →</a>
        </div>
    </div>
</div>
@endsection 