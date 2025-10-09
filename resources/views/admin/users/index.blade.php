<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Accounts Management') }}
            </h2>
            <!-- <div class="flex gap-2">               
                <a href="{{ route('admin.users.create') }}" 
                   class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200">
                    <i class="fas fa-plus mr-2"></i>{{ __('Add User') }}
                </a>
            </div> -->
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Search and Filter -->
                    <form method="GET" class="mb-6 flex gap-4">
                        <div class="flex-1">
                            <input type="text" 
                                   name="search"
                                   value="{{ request('search') }}"
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900"
                                   placeholder="Search users...">
                        </div>
                        <select name="role" class="rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                            <option value="all" {{ request('role') == 'all' ? 'selected' : '' }}>All Roles</option>
                            <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="counselor" {{ request('role') == 'counselor' ? 'selected' : '' }}>Counselor</option>
                            <option value="student" {{ request('role') == 'student' ? 'selected' : '' }}>Student</option>
                        </select>
                        <x-primary-button type="submit">
                            <i class="fas fa-search mr-2"></i>Search
                        </x-primary-button>
                    </form>

                    <!-- Users Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr class="text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    <th class="px-6 py-3">Name</th>
                                    <th class="px-6 py-3">Email</th>
                                    <th class="px-6 py-3">Role</th>
                                    <th class="px-6 py-3">Status</th>
                                    <th class="px-6 py-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($users as $user)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="w-8 h-8 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                                    <span class="text-sm font-medium">
                                                        {{ substr($user->first_name, 0, 1) }}
                                                    </span>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-200">
                                                        {{ $user->first_name }} {{ $user->last_name }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            {{ $user->email }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $user->role === 'admin' ? 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300' : 
                                                   ($user->role === 'counselor' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300' : 
                                                   'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300') }}">
                                                {{ ucfirst($user->role) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($user->status === 'active')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                            bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300">
                                                    Active
                                                </span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                            bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300">
                                                    Inactive
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            <div class="flex items-center gap-3">
                                                <!-- View Button -->
                                                <a href="{{ route('admin.users.show', $user) }}" 
                                                class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300"
                                                title="View User">
                                                    <i class="fas fa-eye"></i>
                                                </a>

                                                <!-- Edit Button -->
                                                <a href="{{ route('admin.users.edit', $user) }}" 
                                                class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300"
                                                title="Edit User">
                                                    <i class="fas fa-edit"></i>
                                                </a>

                                                <!-- Delete Button -->
                                                @if($user->status === 'active')
                                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="text-yellow-600 dark:text-yellow-400 hover:text-yellow-900 dark:hover:text-yellow-300"
                                                                onclick="return confirm('Are you sure you want to deactivate this account?')"
                                                                title="Deactivate Account">
                                                            <i class="fas fa-user-slash"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('admin.users.reactivate', $user) }}" method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit" 
                                                                class="text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300"
                                                                onclick="return confirm('Reactivate this account?')"
                                                                title="Reactivate Account">
                                                            <i class="fas fa-user-check"></i>
                                                        </button>
                                                    </form>
                                                @endif

                                            </div>
                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Import Modal -->
    <x-modal name="import-users" :show="$errors->isNotEmpty()" focusable>
        <form method="POST" action="{{ route('admin.users.import') }}" class="p-6" enctype="multipart/form-data">
            @csrf

            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                {{ __('Import Users') }}
            </h2>

            <div class="mb-6">
                <x-input-label for="file" :value="__('CSV File')" />
                <input type="file" 
                       id="file" 
                       name="file" 
                       accept=".csv" 
                       class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" />
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('Upload a CSV file with the following columns: first_name, middle_name, last_name, email, password, role') }}
                </p>
                <x-input-error :messages="$errors->get('file')" class="mt-2" />
            </div>

            <!-- Download Template Link -->
            <div class="mb-6">
                <a href="{{ route('admin.users.template') }}" 
                   class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                    <i class="fas fa-download mr-1"></i>
                    {{ __('Download CSV Template') }}
                </a>
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-primary-button class="ml-3">
                    {{ __('Import') }}
                </x-primary-button>
            </div>
        </form>
    </x-modal>
</x-app-layout>
