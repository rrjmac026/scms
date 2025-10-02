<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Counselor Management') }}
            </h2>
            <a href="{{ route('admin.counselors.create') }}" 
               class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200">
                <i class="fas fa-plus mr-2"></i>{{ __('Add Counselor') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Search and Filter Form -->
                    <form method="GET" class="mb-6 flex gap-4">
                        <div class="flex-1">
                            <input type="text" 
                                   name="search"
                                   value="{{ request('search') }}"
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900"
                                   placeholder="Search counselors...">
                        </div>
                        <select name="specialization" class="rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                            <option value="">All Specializations</option>
                            @foreach($specializations ?? [] as $spec)
                                <option value="{{ $spec }}" {{ request('specialization') == $spec ? 'selected' : '' }}>
                                    {{ $spec }}
                                </option>
                            @endforeach
                        </select>
                        <x-primary-button type="submit">
                            <i class="fas fa-search mr-2"></i>Search
                        </x-primary-button>
                    </form>

                    <!-- Counselors Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-700">
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Employee ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                        Counseling Category
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($counselors as $counselor)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <div class="h-10 w-10 rounded-full bg-red-100 dark:bg-red-800 flex items-center justify-center">
                                                        <span class="text-sm font-medium text-red-600 dark:text-red-200">
                                                            {{ substr($counselor->user->first_name, 0, 1) }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                        {{ $counselor->user->first_name }} {{ $counselor->user->last_name }}
                                                    </div>
                                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                                        {{ $counselor->user->email }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $counselor->employee_number }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $counselor->category->name ?? 'Unassigned' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300">
                                                Active
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex items-center gap-3">
                                                <a href="{{ route('admin.counselors.show', $counselor) }}" 
                                                   class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.counselors.edit', $counselor) }}" 
                                                   class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.counselors.destroy', $counselor) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300"
                                                            onclick="return confirm('Are you sure you want to delete this counselor?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $counselors->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
