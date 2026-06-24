<x-layouts::app :title="__('Manage Team for :name', ['name' => $dataSource->name])">
    <div class="space-y-6">
        <!-- Header Section -->
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                    {{ __('Manage Team') }}
                </h1>
                <p class="mt-2 text-gray-600 dark:text-gray-400">
                    {{ __('Assign Data Custodian for this data source.') }}
                </p>
            </div>
        </div>

        <!-- Current Team Card -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                {{ __('Current Team') }}
            </h2>

            <div class="grid grid-cols-1 gap-6">
                <div class="bg-gray-50 dark:bg-zinc-700 rounded-lg p-4">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                        {{ __('Data Custodian') }}
                    </h3>
                    <p class="text-gray-900 dark:text-gray-100">
                        {{ $dataSource->dataCustodian()->first()?->name ?? '-' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Team Form -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                {{ __('Update Team Assignments') }}
            </h2>

            <form method="POST" action="{{ route('web.data-sources.team.update', $dataSource) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label for="data_custodian_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ __('Data Custodian') }}
                    </label>
                    <select
                        id="data_custodian_id"
                        name="data_custodian_id"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-zinc-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('data_custodian_id') border-red-500 dark:border-red-500 @enderror"
                    >
                        <option value="">{{ __('-- Select Data Custodian --') }}</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}"
                                {{ old('data_custodian_id', $dataSource->dataCustodian()->first()?->id) == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('data_custodian_id')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end space-x-2 pt-4">
                    <a
                        href="{{ route('web.data-sources.show', $dataSource) }}"
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
            <a href="{{ route('web.data-sources.show', $dataSource) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                {{ __('Back to Data Source') }}
            </a>
        </div>
    </div>
</x-layouts::app>
