<div class="bg-white dark:bg-gray-800 rounded-lg shadow">
    <div class="p-6">
        <div class="flex flex-col md:flex-row">
            <div class="md:w-1/6 flex justify-center mb-4 md:mb-0">
                <img src="https://static.vecteezy.com/system/resources/previews/003/715/527/non_2x/picture-profile-icon-male-icon-human-or-people-sign-and-symbol-vector.jpg" 
                     class="w-32 h-32 rounded-full object-cover border-4 border-gray-200 dark:border-gray-700">
            </div>
            <div class="md:w-5/6 md:pl-6">
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $user->username }}</h3>
                <p class="text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                
                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm">
                            <span class="font-medium text-gray-700 dark:text-gray-300">Status:</span>
                            <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $user->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </p>
                        <p class="text-sm mt-2">
                            <span class="font-medium text-gray-700 dark:text-gray-300">Role:</span>
                            <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $user->is_admin ? 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200' }}">
                                {{ $user->is_admin ? 'Administrator' : 'User' }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <p class="text-sm">
                            <span class="font-medium text-gray-700 dark:text-gray-300">Last Login:</span>
                            <span class="ml-2 text-gray-600 dark:text-gray-400">
                                {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}
                            </span>
                        </p>
                        <p class="text-sm mt-2">
                            <span class="font-medium text-gray-700 dark:text-gray-300">Member Since:</span>
                            <span class="ml-2 text-gray-600 dark:text-gray-400">
                                {{ $user->created_at->format('M d, Y') }}
                            </span>
                        </p>
                    </div>
                </div>

                @if($user->bio)
                    <div class="mt-4">
                        <h5 class="text-lg font-medium text-gray-900 dark:text-white">Bio</h5>
                        <p class="mt-1 text-gray-600 dark:text-gray-400">{{ $user->bio }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div> 