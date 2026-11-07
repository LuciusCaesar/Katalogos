<x-layouts::app :title="__('Domains')">
    <div class="space-y-6">
        {{-- Page header --}}
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                {{ __('Domains') }}
            </h1>
            <flux:button :href="route('web.domains.create')" variant="primary" icon="plus">
                {{ __('New') }}
            </flux:button>
        </div>

        <x-catalog.flash />

        @if ($domains->isEmpty())
            <x-catalog.empty-state
                :message="request()->routeIs('web.my-domains')
                    ? __('No domains assigned to you yet.')
                    : __('No domains found. Create one to get started.')"
            />
        @else
            <x-catalog.table :columns="[
                __('Name'),
                __('Description'),
                __('Business Terms'),
                __('Domain Owner'),
                ['label' => __('Actions'), 'align' => 'right'],
            ]">
                @foreach ($domains as $domain)
                    <tr class="hover:bg-gray-50 dark:hover:bg-zinc-700">
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                            <a href="{{ route('web.domains.show', $domain) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                {{ $domain->name }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $domain->description ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            <a href="{{ route('web.domains.show', $domain) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                {{ $domain->business_assets_count }} {{ __('Business Term', ['count' => $domain->business_assets_count]) }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $domain->domainOwner()->first()?->name ?? '-' }}
                        </td>
                        <x-catalog.row-actions
                            :view-url="route('web.domains.show', $domain)"
                            :edit-url="route('web.domains.edit', $domain)"
                            :delete-url="route('web.domains.destroy', $domain)"
                            :delete-message="__('Are you sure you want to delete this domain?')"
                            :team-url="route('web.domains.team', $domain)"
                        />
                    </tr>
                @endforeach
            </x-catalog.table>

            {{-- Pagination --}}
            <div class="pt-4">
                {{ $domains->links() }}
            </div>
        @endif
    </div>
</x-layouts::app>
