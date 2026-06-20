@props([
    'rootCause' => null,
    'dataIssues',
])

<form method="POST" action="{{ $rootCause ? route('web.root-causes.update', $rootCause) : route('web.root-causes.store') }}" class="space-y-6">
    @csrf
    @if ($rootCause)
        @method('PUT')
    @endif

    <!-- Name Field -->
    <div>
        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            {{ __('Root Cause Name') }}
        </label>
        <input 
            type="text"
            id="name"
            name="name"
            value="{{ old('name', $rootCause?->name) }}"
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
        >{{ old('description', $rootCause?->description) }}</textarea>
        @error('description')
            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <!-- Dimension Field -->
    <div>
        <label for="dimension" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            {{ __('Dimension') }}
        </label>
        <select 
            id="dimension"
            name="dimension"
            required
            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-zinc-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('dimension') border-red-500 dark:border-red-500 @enderror"
        >
            <option value="" disabled {{ old('dimension', $rootCause?->dimension) ? '' : 'selected' }}>
                {{ __('Select Dimension') }}
            </option>
            <option value="Process" {{ old('dimension', $rootCause?->dimension) === 'Process' ? 'selected' : '' }}>
                {{ __('Process') }}
            </option>
            <option value="People" {{ old('dimension', $rootCause?->dimension) === 'People' ? 'selected' : '' }}>
                {{ __('People') }}
            </option>
            <option value="Tool" {{ old('dimension', $rootCause?->dimension) === 'Tool' ? 'selected' : '' }}>
                {{ __('Tool') }}
            </option>
        </select>
        @error('dimension')
            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <!-- Data Issues Field -->
    <div>
        <label for="data_issue_ids" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            {{ __('Data Issues') }}
        </label>
        <select 
            id="data_issue_ids"
            name="data_issue_ids[]"
            multiple
            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-zinc-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('data_issue_ids') border-red-500 dark:border-red-500 @enderror"
        >
            @foreach ($dataIssues as $dataIssue)
                <option value="{{ $dataIssue->id }}" {{ in_array($dataIssue->id, old('data_issue_ids', $rootCause?->dataIssues->pluck('id')->toArray() ?? [])) ? 'selected' : '' }}>
                    {{ $dataIssue->name }}
                </option>
            @endforeach
        </select>
        @error('data_issue_ids')
            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <!-- Submit Button -->
    <div class="flex justify-end">
        <button 
            type="submit"
            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
        >
            {{ $rootCause ? __('Update Root Cause') : __('Create Root Cause') }}
        </button>
    </div>
</form>
