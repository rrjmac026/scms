<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Book Appointment') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('student.appointments.store') }}" method="POST" class="space-y-6">
                        @csrf
                        
                        <!-- Counselor Selection -->
                        <div>
                            <x-input-label for="counselor_id" :value="__('Select Counselor')" />
                            <select id="counselor_id" name="counselor_id" required
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm focus:border-pink-500 focus:ring focus:ring-pink-200 dark:focus:ring-pink-800">
                                <option value="">Choose a counselor</option>
                                @foreach($counselors as $counselor)
                                    <option value="{{ $counselor->id }}">
                                        {{ $counselor->user->name }} - {{ $counselor->specialization }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('counselor_id')" class="mt-2" />
                        </div>

                        <!-- Preferred Date -->
                        <div>
                            <x-input-label for="preferred_date" :value="__('Preferred Date')" />
                            <x-text-input id="preferred_date" name="preferred_date" type="date" 
                                class="mt-1 block w-full" required min="{{ date('Y-m-d') }}" />
                            <x-input-error :messages="$errors->get('preferred_date')" class="mt-2" />
                        </div>

                        <!-- Preferred Time -->
                        <div>
                            <x-input-label for="preferred_time" :value="__('Preferred Time')" />
                            <select id="preferred_time" name="preferred_time" required
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm focus:border-pink-500 focus:ring focus:ring-pink-200 dark:focus:ring-pink-800">
                                <option value="">Select time slot</option>
                                <option value="09:00 AM">9:00 AM</option>
                                <option value="10:00 AM">10:00 AM</option>
                                <option value="11:00 AM">11:00 AM</option>
                                <option value="01:00 PM">1:00 PM</option>
                                <option value="02:00 PM">2:00 PM</option>
                                <option value="03:00 PM">3:00 PM</option>
                                <option value="04:00 PM">4:00 PM</option>
                            </select>
                            <x-input-error :messages="$errors->get('preferred_time')" class="mt-2" />
                        </div>

                        <!-- Concern/Reason -->
                        <div>
                            <x-input-label for="concern" :value="__('Reason for Appointment')" />
                            <textarea id="concern" name="concern" rows="4" required
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm focus:border-pink-500 focus:ring focus:ring-pink-200 dark:focus:ring-pink-800"
                                placeholder="Please briefly describe your reason for seeking counseling..."></textarea>
                            <x-input-error :messages="$errors->get('concern')" class="mt-2" />
                        </div>

                        <div class="flex justify-end gap-4">
                            <x-secondary-button onclick="history.back()">
                                {{ __('Cancel') }}
                            </x-secondary-button>
                            <x-primary-button>
                                {{ __('Book Appointment') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
