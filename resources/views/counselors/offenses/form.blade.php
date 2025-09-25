<div class="grid grid-cols-1 gap-6">
    <!-- Student Selection -->
    <div>
        <x-input-label for="student_id" :value="__('Student')" />
        <select id="student_id" name="student_id" required
                class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-pink-500 focus:ring-pink-500">
            <option value="">Select Student</option>
            @foreach($students as $student)
                <option value="{{ $student->id }}" 
                    {{ (old('student_id', $offense->student_id ?? '') == $student->id) ? 'selected' : '' }}>
                    {{ $student->user->name }} ({{ $student->student_number }})
                </option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('student_id')" class="mt-2" />
    </div>

    <!-- Offense -->
    <div>
        <x-input-label for="offense" :value="__('Offense Description')" />
        <x-text-input id="offense" name="offense" type="text" class="mt-1 block w-full"
            :value="old('offense', $offense->offense ?? '')" required />
        <x-input-error :messages="$errors->get('offense')" class="mt-2" />
    </div>

    <!-- Date -->
    <div>
        <x-input-label for="date" :value="__('Date of Offense')" />
        <x-text-input id="date" name="date" type="date" class="mt-1 block w-full"
            :value="old('date', isset($offense) ? $offense->date->format('Y-m-d') : '')" required />
        <x-input-error :messages="$errors->get('date')" class="mt-2" />
    </div>

    <!-- Remarks -->
    <div>
        <x-input-label for="remarks" :value="__('Remarks')" />
        <textarea id="remarks" name="remarks" rows="3" 
            class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-pink-500 focus:ring-pink-500">{{ old('remarks', $offense->remarks ?? '') }}</textarea>
        <x-input-error :messages="$errors->get('remarks')" class="mt-2" />
    </div>

    <!-- Solution -->
    <div>
        <x-input-label for="solution" :value="__('Solution/Resolution')" />
        <textarea id="solution" name="solution" rows="3"
            class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-pink-500 focus:ring-pink-500">{{ old('solution', $offense->solution ?? '') }}</textarea>
        <x-input-error :messages="$errors->get('solution')" class="mt-2" />
    </div>

    <!-- Status -->
    <div>
        <x-input-label for="resolved" :value="__('Status')" />
        <div class="mt-2">
            <label class="inline-flex items-center">
                <input type="checkbox" name="resolved" value="1" class="rounded border-gray-300 text-pink-600 shadow-sm focus:ring-pink-500"
                    {{ old('resolved', $offense->resolved ?? false) ? 'checked' : '' }}>
                <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Mark as resolved</span>
            </label>
        </div>
    </div>
</div>
