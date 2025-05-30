@extends('manage.accounts.details')

@section('tab-content')
<form action="{{ route('manage.accounts.set-admin', $user->id) }}" method="POST" class="space-y-6">
    @csrf
    @method('PUT')

    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Admin Privileges</h3>
    
    <div class="space-y-4">
        <div class="flex items-center">
            <input type="hidden" name="is_admin" value="0">
            <input type="checkbox" name="is_admin" id="is_admin" value="1"
                {{ $user->is_admin ? 'checked' : '' }}
                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600 rounded">
            <label for="is_admin" class="ml-3 block text-sm font-medium text-gray-700 dark:text-gray-300">
                Grant Administrator Privileges
            </label>
        </div>

        <div class="mt-4 p-4 bg-yellow-50 dark:bg-yellow-900/50 rounded-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400 dark:text-yellow-300" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Warning</h3>
                    <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                        <p>Granting administrator privileges will give this user full access to manage the system. Please be certain before proceeding.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-6 flex justify-end">
        <button type="submit" 
                class="px-4 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition duration-150 ease-in-out shadow-sm">
            Update Privileges
        </button>
    </div>
</form>
@endsection 