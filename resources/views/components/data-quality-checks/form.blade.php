@props([
    'dataQualityCheck' => null,
    'businessRules',
    'dataSources',
])

<form method="POST" action="{{ $dataQualityCheck ? route('web.data-quality-checks.update', $dataQualityCheck) : route('web.data-quality-checks.store') }}" class="space-y-6">
    @csrf
    @if ($dataQualityCheck)
        @method('PUT')
    @endif

    <!-- Name Field -->
    <div>
        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            {{ __('Data Quality Check Name') }}
        </label>
        <input 
            type="text"
            id="name"
            name="name"
            value="{{ old('name', $dataQualityCheck?->name) }}"
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
            rows="3"
            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-zinc-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('description') border-red-500 dark:border-red-500 @enderror"
        >{{ old('description', $dataQualityCheck?->description) }}</textarea>
        @error('description')
            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <!-- Business Rule Field -->
    <div>
        <label for="business_rule_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            {{ __('Business Rule') }}
        </label>
        <select 
            id="business_rule_id"
            name="business_rule_id"
            required
            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-zinc-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('business_rule_id') border-red-500 dark:border-red-500 @enderror"
        >
            <option value="">{{ __('Select a Business Rule') }}</option>
            @foreach ($businessRules as $businessRule)
                <option value="{{ $businessRule->id }}" {{ old('business_rule_id', $dataQualityCheck?->business_rule_id) == $businessRule->id ? 'selected' : '' }}>
                    {{ $businessRule->name }}
                </option>
            @endforeach
        </select>
        @error('business_rule_id')
            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <!-- Data Sources Field -->
    <div>
        <label for="data_source_ids" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            {{ __('Data Sources') }}
        </label>
        <select 
            id="data_source_ids"
            name="data_source_ids[]"
            multiple
            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-zinc-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('data_source_ids') border-red-500 dark:border-red-500 @enderror"
        >
            @foreach ($dataSources as $dataSource)
                <option value="{{ $dataSource->id }}" {{ in_array($dataSource->id, old('data_source_ids', $dataQualityCheck?->dataSources->pluck('id')->toArray() ?? [])) ? 'selected' : '' }}>
                    {{ $dataSource->name }}
                </option>
            @endforeach
        </select>
        @error('data_source_ids')
            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <!-- Submit Button -->
    <div class="flex justify-end">
        <button 
            type="submit"
            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
        >
            {{ $dataQualityCheck ? __('Update Data Quality Check') : __('Create Data Quality Check') }}
        </button>
    </div>
</form>
