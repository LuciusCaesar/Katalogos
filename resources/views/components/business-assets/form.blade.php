@props([
    'businessAsset' => null,
    'domains',
    'dataInitiatives',
])

<form method="POST" action="{{ $businessAsset ? route('web.business-assets.update', $businessAsset) : route('web.business-assets.store') }}" class="space-y-6">
    @csrf
    @if ($businessAsset)
        @method('PUT')
    @endif

    <!-- Name Field -->
    <div>
        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            {{ __('Business Asset Name') }}
        </label>
        <input 
            type="text"
            id="name"
            name="name"
            value="{{ old('name', $businessAsset?->name) }}"
            required
            autofocus
            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-zinc-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('name') border-red-500 dark:border-red-500 @enderror"
        >
        @error('name')
            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <!-- Definition Field -->
    <div>
        <label for="definition" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            {{ __('Definition') }}
        </label>
        <textarea 
            id="definition"
            name="definition"
            rows="3"
            required
            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-zinc-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('definition') border-red-500 dark:border-red-500 @enderror"
        >{{ old('definition', $businessAsset?->definition) }}</textarea>
        @error('definition')
            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <!-- Domain Field -->
    <div>
        <label for="domain_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            {{ __('Domain') }}
        </label>
        <select 
            id="domain_id"
            name="domain_id"
            required
            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-zinc-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('domain_id') border-red-500 dark:border-red-500 @enderror"
        >
            <option value="">{{ __('Select a Domain') }}</option>
            @foreach ($domains as $domain)
                <option value="{{ $domain->id }}" {{ old('domain_id', $businessAsset?->domain_id) == $domain->id ? 'selected' : '' }}>
                    {{ $domain->name }}
                </option>
            @endforeach
        </select>
        @error('domain_id')
            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <!-- Data Initiative Field -->
    <div>
        <label for="data_initiative_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            {{ __('Data Initiative') }}
        </label>
        <select 
            id="data_initiative_id"
            name="data_initiative_id"
            required
            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-zinc-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('data_initiative_id') border-red-500 dark:border-red-500 @enderror"
        >
            <option value="">{{ __('Select a Data Initiative') }}</option>
            @foreach ($dataInitiatives as $dataInitiative)
                <option value="{{ $dataInitiative->id }}" {{ old('data_initiative_id', $businessAsset?->data_initiative_id) == $dataInitiative->id ? 'selected' : '' }}>
                    {{ $dataInitiative->label }}
                </option>
            @endforeach
        </select>
        @error('data_initiative_id')
            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <!-- Categorization Fields -->
    <div class="bg-neutral-50 dark:bg-zinc-700 p-4 rounded-lg border border-neutral-200 dark:border-neutral-600">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('Categorization Level') }}</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Consistency Field -->
            <div>
                <label for="consistency" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ __('Consistency') }}
                </label>
                <select 
                    id="consistency"
                    name="consistency"
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-zinc-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('consistency') border-red-500 dark:border-red-500 @enderror"
                >
                    <option value="">{{ __('Select Level') }}</option>
                    <option value="low" {{ old('consistency', $businessAsset?->consistency) == 'low' ? 'selected' : '' }}>
                        {{ __('Low') }}
                    </option>
                    <option value="middle" {{ old('consistency', $businessAsset?->consistency) == 'middle' ? 'selected' : '' }}>
                        {{ __('Middle') }}
                    </option>
                    <option value="high" {{ old('consistency', $businessAsset?->consistency) == 'high' ? 'selected' : '' }}>
                        {{ __('High') }}
                    </option>
                </select>
                @error('consistency')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Integrity Field -->
            <div>
                <label for="integrity" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ __('Integrity') }}
                </label>
                <select 
                    id="integrity"
                    name="integrity"
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-zinc-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('integrity') border-red-500 dark:border-red-500 @enderror"
                >
                    <option value="">{{ __('Select Level') }}</option>
                    <option value="low" {{ old('integrity', $businessAsset?->integrity) == 'low' ? 'selected' : '' }}>
                        {{ __('Low') }}
                    </option>
                    <option value="middle" {{ old('integrity', $businessAsset?->integrity) == 'middle' ? 'selected' : '' }}>
                        {{ __('Middle') }}
                    </option>
                    <option value="high" {{ old('integrity', $businessAsset?->integrity) == 'high' ? 'selected' : '' }}>
                        {{ __('High') }}
                    </option>
                </select>
                @error('integrity')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Availability Field -->
            <div>
                <label for="availability" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ __('Availability') }}
                </label>
                <select 
                    id="availability"
                    name="availability"
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-zinc-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('availability') border-red-500 dark:border-red-500 @enderror"
                >
                    <option value="">{{ __('Select Level') }}</option>
                    <option value="low" {{ old('availability', $businessAsset?->availability) == 'low' ? 'selected' : '' }}>
                        {{ __('Low') }}
                    </option>
                    <option value="middle" {{ old('availability', $businessAsset?->availability) == 'middle' ? 'selected' : '' }}>
                        {{ __('Middle') }}
                    </option>
                    <option value="high" {{ old('availability', $businessAsset?->availability) == 'high' ? 'selected' : '' }}>
                        {{ __('High') }}
                    </option>
                </select>
                @error('availability')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <!-- Submit Button -->
    <div class="flex justify-end">
        <button 
            type="submit"
            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
        >
            {{ $businessAsset ? __('Update Business Asset') : __('Create Business Asset') }}
        </button>
    </div>
</form>
