<x-layouts::app :title="__('Data Sources')">
    <div class="space-y-6">
        {{-- Page header --}}
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                {{ __('Data Sources') }}
            </h1>
            <flux:button :href="route('web.data-sources.create')" variant="primary" icon="plus">
                {{ __('New') }}
            </flux:button>
        </div>

        <x-catalog.flash />

        @if ($dataSources->isEmpty())
            <x-catalog.empty-state
                :message="request()->routeIs('web.my-sources')
                    ? __('No data sources assigned to you yet.')
                    : __('No data sources found. Create one to get started.')"
            />
        @else
            <x-catalog.table :columns="[
                __('Name'),
                __('Description'),
                __('Business Assets'),
                __('Data Custodian'),
                ['label' => __('Actions'), 'align' => 'right'],
            ]">
                @foreach ($dataSources as $dataSource)
                    <tr class="hover:bg-gray-50 dark:hover:bg-zinc-700">
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                            <a href="{{ route('web.data-sources.show', $dataSource) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                {{ $dataSource->name }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $dataSource->description ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            <a href="{{ route('web.data-sources.show', $dataSource) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                {{ $dataSource->business_assets_count }} {{ __('Business Asset', ['count' => $dataSource->business_assets_count]) }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $dataSource->dataCustodian()->first()?->name ?? '-' }}
                        </td>
                        <x-catalog.row-actions
                            :view-url="route('web.data-sources.show', $dataSource)"
                            :edit-url="route('web.data-sources.edit', $dataSource)"
                            :delete-url="route('web.data-sources.destroy', $dataSource)"
                            :delete-message="__('Are you sure you want to delete this data source?')"
                            :team-url="route('web.data-sources.team', $dataSource)"
                        />
                    </tr>
                @endforeach
            </x-catalog.table>

            {{-- Pagination --}}
            <div class="pt-4">
                {{ $dataSources->links() }}
            </div>
        @endif
    </div>
</x-layouts::app>
