@props([
    'dataInitiative' => null,
    'businessObjectives' => null,
    'selectedBusinessObjectiveIds' => [],
])

<form method="POST" action="{{ $dataInitiative ? route('web.data-initiatives.update', $dataInitiative) : route('web.data-initiatives.store') }}" class="space-y-6">
    @csrf
    @if ($dataInitiative)
        @method('PUT')
    @endif

    <!-- Code Field -->
    <div>
        <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            {{ __('Code') }}
        </label>
        <input 
            type="text"
            id="code"
            name="code"
            value="{{ old('code', $dataInitiative?->code) }}"
            required
            autofocus
            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-zinc-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('code') border-red-500 dark:border-red-500 @enderror"
        >
        @error('code')
            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <!-- Label Field -->
    <div>
        <label for="label" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            {{ __('Label') }}
        </label>
        <input 
            type="text"
            id="label"
            name="label"
            value="{{ old('label', $dataInitiative?->label) }}"
            required
            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-zinc-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('label') border-red-500 dark:border-red-500 @enderror"
        >
        @error('label')
            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <!-- Description Field -->
    <div>
        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            {{ __('Description') }}
        </label>
        <textarea 
            id="description"
            name="description"
            rows="5"
            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-zinc-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('description') border-red-500 dark:border-red-500 @enderror"
        >{{ old('description', $dataInitiative?->description) }}</textarea>
        @error('description')
            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <!-- Business Objectives Field -->
    @if($businessObjectives && $businessObjectives->isNotEmpty())
        <div>
            <label for="business_objective_ids" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ __('Business Objectives') }}
            </label>
            <select 
                id="business_objective_ids"
                name="business_objective_ids[]"
                multiple
                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-zinc-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('business_objective_ids') border-red-500 dark:border-red-500 @enderror"
                size="5"
            >
                @foreach($businessObjectives as $businessObjective)
                    <option value="{{ $businessObjective->id }}" 
                        @if(in_array($businessObjective->id, old('business_objective_ids', $selectedBusinessObjectiveIds ?? []))) selected @endif
                    >
                        {{ $businessObjective->name }}
                    </option>
                @endforeach
            </select>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                {{ __('Hold Ctrl/Cmd to select multiple business objectives') }}
            </p>
            @error('business_objective_ids')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>
    @endif

    <!-- Submit Button -->
    <div class="flex justify-end">
        <button 
            type="submit"
            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
        >
            {{ $dataInitiative ? __('Update Data Initiative') : __('Create Data Initiative') }}
        </button>
    </div>
</form>
