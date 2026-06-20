@props([
    'solution' => null,
    'rootCauses',
])

<form method="POST" action="{{ $solution ? route('web.solutions.update', $solution) : route('web.solutions.store') }}" class="space-y-6">
    @csrf
    @if ($solution)
        @method('PUT')
    @endif

    <!-- Name Field -->
    <div>
        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            {{ __('Solution Name') }}
        </label>
        <input 
            type="text"
            id="name"
            name="name"
            value="{{ old('name', $solution?->name) }}"
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
        >{{ old('description', $solution?->description) }}</textarea>
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
            <option value="" disabled {{ old('dimension', $solution?->dimension) ? '' : 'selected' }}>
                {{ __('Select Dimension') }}
            </option>
            <option value="Process" {{ old('dimension', $solution?->dimension) === 'Process' ? 'selected' : '' }}>
                {{ __('Process') }}
            </option>
            <option value="People" {{ old('dimension', $solution?->dimension) === 'People' ? 'selected' : '' }}>
                {{ __('People') }}
            </option>
            <option value="Tool" {{ old('dimension', $solution?->dimension) === 'Tool' ? 'selected' : '' }}>
                {{ __('Tool') }}
            </option>
        </select>
        @error('dimension')
            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <!-- Root Causes Field -->
    <div>
        <label for="root_cause_ids" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            {{ __('Root Causes') }}
        </label>
        <select 
            id="root_cause_ids"
            name="root_cause_ids[]"
            multiple
            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-zinc-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('root_cause_ids') border-red-500 dark:border-red-500 @enderror"
        >
            @foreach ($rootCauses as $rootCause)
                <option value="{{ $rootCause->id }}" {{ in_array($rootCause->id, old('root_cause_ids', $solution?->rootCauses->pluck('id')->toArray() ?? [])) ? 'selected' : '' }}>
                    {{ $rootCause->name }}
                </option>
            @endforeach
        </select>
        @error('root_cause_ids')
            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <!-- Submit Button -->
    <div class="flex justify-end">
        <button 
            type="submit"
            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
        >
            {{ $solution ? __('Update Solution') : __('Create Solution') }}
        </button>
    </div>
</form>
