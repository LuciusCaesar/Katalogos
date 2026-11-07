<x-layouts::app :title="$dataSource->name">
    <x-catalog.show-layout
        :title="$dataSource->name"
        type-label="__('Data Source')"
        :breadcrumbs="[
            ['label' => __('Data Sources'), 'href' => route('web.data-sources.index')],
            ['label' => $dataSource->name, 'href' => null],
        ]"
        :definition="$dataSource->description"
        :edit-url="route('web.data-sources.edit', $dataSource)"
        :delete-url="route('web.data-sources.destroy', $dataSource)"
        :delete-message="__('Are you sure you want to delete this data source?')"
        :diagram-url="route('web.impact-diagram.show', ['entityType' => 'data-source', 'entity' => $dataSource])"
        :team-url="route('web.data-sources.team', $dataSource)"
        :back-url="route('web.data-sources.index')"
        back-label="__('Back to Data Sources')"
    >
        <x-slot:responsibilities>
            <x-catalog.responsibilities
                :groups="[
                    ['role' => __('Data Custodian'), 'users' => $dataSource->dataCustodian],
                ]"
            />
        </x-slot:responsibilities>

        {{-- Business Assets --}}
        <x-catalog.section
            :title="__('Business Assets')"
            :count="$dataSource->businessAssets->count()"
            :empty-message="__('No business assets associated with this data source.')"
            :empty="$dataSource->businessAssets->isEmpty()"
        >
            <x-catalog.table :columns="[__('Name'), __('Definition'), __('Domain'), __('Data Initiative')]">
                @foreach ($dataSource->businessAssets as $asset)
                    <tr class="hover:bg-gray-50 dark:hover:bg-zinc-700">
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                            <a href="{{ route('web.business-assets.show', $asset) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                {{ $asset->name }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $asset->definition ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $asset->domain?->name ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $asset->dataInitiative?->label ?? '-' }}
                        </td>
                    </tr>
                @endforeach
            </x-catalog.table>
        </x-catalog.section>

        <x-slot:meta>
            <x-catalog.timestamps :model="$dataSource" />
        </x-slot:meta>
    </x-catalog.show-layout>
</x-layouts::app>
