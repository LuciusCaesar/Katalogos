<x-layouts::app :title="__('Manage Team for :name', ['name' => $dataInitiative->label])">
    <div class="space-y-6">
        <x-catalog.breadcrumbs
            :items="[
                ['label' => __('Data Initiatives'), 'href' => route('web.data-initiatives.index')],
                ['label' => $dataInitiative->label, 'href' => route('web.data-initiatives.show', $dataInitiative)],
                ['label' => __('Manage Team'), 'href' => null],
            ]"
        />

        {{-- Header --}}
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                {{ __('Manage Team') }}
            </h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                {{ __('Assign Data Steward and Data Owner for this data initiative.') }}
            </p>
        </div>

        {{-- Current Team --}}
        <flux:card class="space-y-4">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ __('Current Team') }}
            </h2>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div class="rounded-lg bg-gray-50 p-4 dark:bg-zinc-700">
                    <h3 class="mb-2 text-sm font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        {{ __('Data Steward') }}
                    </h3>
                    <x-catalog.people :users="$dataInitiative->dataSteward" />
                </div>

                <div class="rounded-lg bg-gray-50 p-4 dark:bg-zinc-700">
                    <h3 class="mb-2 text-sm font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        {{ __('Data Owner') }}
                    </h3>
                    <x-catalog.people :users="$dataInitiative->dataOwner" />
                </div>
            </div>
        </flux:card>

        {{-- Team Form --}}
        <flux:card class="space-y-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ __('Update Team Assignments') }}
            </h2>

            <form method="POST" action="{{ route('web.data-initiatives.team.update', $dataInitiative) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label for="data_steward_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ __('Data Steward') }}
                    </label>
                    <select
                        id="data_steward_id"
                        name="data_steward_id"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-zinc-800 dark:text-gray-100 @error('data_steward_id') border-red-500 dark:border-red-500 @enderror"
                    >
                        <option value="">{{ __('-- Select Data Steward --') }}</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}"
                                {{ old('data_steward_id', $dataInitiative->dataSteward()->first()?->id) == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('data_steward_id')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="data_owner_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ __('Data Owner') }}
                    </label>
                    <select
                        id="data_owner_id"
                        name="data_owner_id"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-zinc-800 dark:text-gray-100 @error('data_owner_id') border-red-500 dark:border-red-500 @enderror"
                    >
                        <option value="">{{ __('-- Select Data Owner --') }}</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}"
                                {{ old('data_owner_id', $dataInitiative->dataOwner()->first()?->id) == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('data_owner_id')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end gap-2 pt-4">
                    <flux:button :href="route('web.data-initiatives.show', $dataInitiative)" variant="ghost">
                        {{ __('Cancel') }}
                    </flux:button>
                    <flux:button type="submit" variant="primary">
                        {{ __('Save Team') }}
                    </flux:button>
                </div>
            </form>
        </flux:card>

        {{-- Back link --}}
        <div>
            <flux:button :href="route('web.data-initiatives.show', $dataInitiative)" variant="ghost" icon="arrow-left" size="sm">
                {{ __('Back to Data Initiative') }}
            </flux:button>
        </div>
    </div>
</x-layouts::app>
