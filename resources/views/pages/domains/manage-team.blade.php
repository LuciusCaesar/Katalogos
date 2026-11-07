<x-layouts::app :title="__('Manage Team for :name', ['name' => $domain->name])">
    <div class="space-y-6">
        <x-catalog.breadcrumbs
            :items="[
                ['label' => __('Domains'), 'href' => route('web.domains.index')],
                ['label' => $domain->name, 'href' => route('web.domains.show', $domain)],
                ['label' => __('Manage Team'), 'href' => null],
            ]"
        />

        {{-- Header --}}
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                {{ __('Manage Team') }}
            </h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                {{ __('Assign Domain Owner for this domain.') }}
            </p>
        </div>

        {{-- Current Team --}}
        <flux:card class="space-y-4">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ __('Current Team') }}
            </h2>

            <div class="grid grid-cols-1 gap-6">
                <div class="rounded-lg bg-gray-50 p-4 dark:bg-zinc-700">
                    <h3 class="mb-2 text-sm font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        {{ __('Domain Owner') }}
                    </h3>
                    <x-catalog.people :users="$domain->domainOwner" />
                </div>
            </div>
        </flux:card>

        {{-- Team Form --}}
        <flux:card class="space-y-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ __('Update Team Assignments') }}
            </h2>

            <form method="POST" action="{{ route('web.domains.team.update', $domain) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label for="domain_owner_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ __('Domain Owner') }}
                    </label>
                    <select
                        id="domain_owner_id"
                        name="domain_owner_id"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-zinc-800 dark:text-gray-100 @error('domain_owner_id') border-red-500 dark:border-red-500 @enderror"
                    >
                        <option value="">{{ __('-- Select Domain Owner --') }}</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}"
                                {{ old('domain_owner_id', $domain->domainOwner()->first()?->id) == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('domain_owner_id')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end gap-2 pt-4">
                    <flux:button :href="route('web.domains.show', $domain)" variant="ghost">
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
            <flux:button :href="route('web.domains.show', $domain)" variant="ghost" icon="arrow-left" size="sm">
                {{ __('Back to Domain') }}
            </flux:button>
        </div>
    </div>
</x-layouts::app>
