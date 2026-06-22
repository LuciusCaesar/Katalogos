@props([
    'dataIssue' => null,
    'businessAssets',
    'businessRules',
])

<form method="POST" action="{{ $dataIssue ? route('web.data-issues.update', $dataIssue) : route('web.data-issues.store') }}" class="space-y-6">
    @csrf
    @if ($dataIssue)
        @method('PUT')
    @endif

    <!-- Name Field -->
    <div>
        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            {{ __('Data Issue Name') }}
        </label>
        <input 
            type="text"
            id="name"
            name="name"
            value="{{ old('name', $dataIssue?->name) }}"
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
        >{{ old('description', $dataIssue?->description) }}</textarea>
        @error('description')
            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <!-- Business Assets Field -->
    <div>
        <label for="business_asset_ids" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            {{ __('Business Assets') }}
        </label>
        <select 
            id="business_asset_ids"
            name="business_asset_ids[]"
            multiple
            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-zinc-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('business_asset_ids') border-red-500 dark:border-red-500 @enderror"
        >
            @foreach ($businessAssets as $asset)
                <option value="{{ $asset->id }}" {{ in_array($asset->id, old('business_asset_ids', $dataIssue?->businessAssets->pluck('id')->toArray() ?? [])) ? 'selected' : '' }}>
                    {{ $asset->name }}
                </option>
            @endforeach
        </select>
        @error('business_asset_ids')
            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <!-- Business Rules Field -->
    <div>
        <label for="business_rule_ids" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            {{ __('Business Rules') }}
        </label>
        <select 
            id="business_rule_ids"
            name="business_rule_ids[]"
            multiple
            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-zinc-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('business_rule_ids') border-red-500 dark:border-red-500 @enderror"
        >
            @foreach ($businessRules as $rule)
                <option value="{{ $rule->id }}" {{ in_array($rule->id, old('business_rule_ids', $dataIssue?->businessRules->pluck('id')->toArray() ?? [])) ? 'selected' : '' }}>
                    {{ $rule->name }}
                </option>
            @endforeach
        </select>
        @error('business_rule_ids')
            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <!-- Submit Button -->
    <div class="flex justify-end">
        <button 
            type="submit"
            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
        >
            {{ $dataIssue ? __('Update Data Issue') : __('Create Data Issue') }}
        </button>
    </div>
</form>
