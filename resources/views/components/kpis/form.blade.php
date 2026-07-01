@props([
    'kpi' => null,
    'businessObjectives',
])

<form method="POST" action="{{ $kpi ? route('web.kpis.update', $kpi) : route('web.kpis.store') }}" class="space-y-6">
    @csrf
    @if ($kpi)
        @method('PUT')
    @endif

    <!-- Name Field -->
    <div>
        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            {{ __('Name') }}
        </label>
        <input 
            type="text"
            id="name"
            name="name"
            value="{{ old('name', $kpi?->name) }}"
            required
            autofocus
            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-zinc-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('name') border-red-500 dark:border-red-500 @enderror"
        >
        @error('name')
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
        >{{ old('description', $kpi?->description) }}</textarea>
        @error('description')
            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <!-- Business Objective Field -->
    <div>
        <label for="business_objective_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            {{ __('Business Objective') }}
        </label>
        <select 
            id="business_objective_id"
            name="business_objective_id"
            required
            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-zinc-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('business_objective_id') border-red-500 dark:border-red-500 @enderror"
        >
            <option value="">{{ __('Select a Business Objective') }}</option>
            @foreach ($businessObjectives as $businessObjective)
                <option value="{{ $businessObjective->id }}" {{ old('business_objective_id', $kpi?->business_objective_id) == $businessObjective->id ? 'selected' : '' }}>
                    {{ $businessObjective->name }}
                </option>
            @endforeach
        </select>
        @error('business_objective_id')
            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <!-- Submit Button -->
    <div class="flex justify-end">
        <button 
            type="submit"
            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
        >
            {{ $kpi ? __('Update KPI') : __('Create KPI') }}
        </button>
    </div>
</form>
