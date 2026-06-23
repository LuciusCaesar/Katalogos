<x-layouts::app :title="__('Manage Team for :name', ['name' => $businessAsset->name])">
    <div class="space-y-6">
        <!-- Header Section -->
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                    {{ __('Manage Team') }}
                </h1>
                <p class="mt-2 text-gray-600 dark:text-gray-400">
                    {{ __('Assign Data Steward and Data Owner for this business asset.') }}
                </p>
            </div>
        </div>

        <!-- Current Team Card -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                {{ __('Current Team') }}
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Data Steward Card -->
                <div class="bg-gray-50 dark:bg-zinc-700 rounded-lg p-4">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                        {{ __('Data Steward') }}
                    </h3>
                    <p class="text-gray-900 dark:text-gray-100">
                        {{ $businessAsset->dataSteward()->first()?->name ?? '-' }}
                    </p>
                </div>
                
                <!-- Data Owner Card -->
                <div class="bg-gray-50 dark:bg-zinc-700 rounded-lg p-4">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                        {{ __('Data Owner') }}
                    </h3>
                    <p class="text-gray-900 dark:text-gray-100">
                        {{ $businessAsset->dataOwner()->first()?->name ?? '-' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Team Form -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                {{ __('Update Team Assignments') }}
            </h2>
            
            <form method="POST" action="{{ route('web.business-assets.team.update', $businessAsset) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Data Steward Select -->
                <div>
                    <label for="data_steward_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ __('Data Steward') }}
                    </label>
                    <select 
                        id="data_steward_id"
                        name="data_steward_id"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-zinc-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('data_steward_id') border-red-500 dark:border-red-500 @enderror"
                    >
                        <option value="">{{ __('-- Select Data Steward --') }}</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" 
                                {{ old('data_steward_id', $businessAsset->dataSteward()->first()?->id) == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('data_steward_id')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Data Owner Select -->
                <div>
                    <label for="data_owner_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ __('Data Owner') }}
                    </label>
                    <select 
                        id="data_owner_id"
                        name="data_owner_id"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-zinc-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('data_owner_id') border-red-500 dark:border-red-500 @enderror"
                    >
                        <option value="">{{ __('-- Select Data Owner --') }}</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" 
                                {{ old('data_owner_id', $businessAsset->dataOwner()->first()?->id) == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('data_owner_id')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-end space-x-2 pt-4">
                    <a 
                        href="{{ route('web.business-assets.show', $businessAsset) }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-500 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-900 uppercase tracking-widest hover:bg-gray-600 dark:hover:bg-gray-500 focus:bg-gray-600 dark:focus:bg-gray-500 active:bg-gray-700 dark:active:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-900 transition ease-in-out duration-150"
                    >
                        {{ __('Cancel') }}
                    </a>
                    <button 
                        type="submit"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-900 uppercase tracking-widest hover:bg-blue-700 dark:hover:bg-blue-600 focus:bg-blue-700 dark:focus:bg-blue-600 active:bg-blue-800 dark:active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-900 transition ease-in-out duration-150"
                    >
                        {{ __('Save Team') }}
                    </button>
                </div>
            </form>
        </div>

        <!-- Back Link -->
        <div>
            <a href="{{ route('web.business-assets.show', $businessAsset) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                {{ __('Back to Business Asset') }}
            </a>
        </div>
    </div>
</x-layouts::app>
