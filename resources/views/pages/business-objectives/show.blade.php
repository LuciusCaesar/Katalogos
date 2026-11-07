<x-layouts::app :title="$businessObjective->name">
    <x-catalog.show-layout
        :title="$businessObjective->name"
        type-label="__('Business Objective')"
        :breadcrumbs="[
            ['label' => __('Business Objectives'), 'href' => route('web.business-objectives.index')],
            ['label' => $businessObjective->name, 'href' => null],
        ]"
        :definition="$businessObjective->description"
        :edit-url="route('web.business-objectives.edit', $businessObjective)"
        :delete-url="route('web.business-objectives.destroy', $businessObjective)"
        :delete-message="__('Are you sure you want to delete this business objective?')"
        :diagram-url="route('web.impact-diagram.show', ['entityType' => 'business-objective', 'entity' => $businessObjective])"
        :back-url="route('web.business-objectives.index')"
        back-label="__('Back to Business Objectives')"
    >
        {{-- Data Initiatives --}}
        <x-catalog.section
            :title="__('Data Initiatives')"
            :count="$businessObjective->dataInitiatives->count()"
            :empty-message="__('No data initiatives associated with this business objective.')"
            :empty="$businessObjective->dataInitiatives->isEmpty()"
        >
            <x-catalog.table :columns="[__('Code'), __('Label'), __('Business Assets')]">
                @foreach ($businessObjective->dataInitiatives as $initiative)
                    <tr class="hover:bg-gray-50 dark:hover:bg-zinc-700">
                        <td class="whitespace-nowrap px-6 py-4 text-sm">
                            <a href="{{ route('web.data-initiatives.show', $initiative) }}" class="text-gray-900 hover:text-blue-600 dark:text-gray-100 dark:hover:text-blue-400">
                                {{ $initiative->code }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $initiative->label }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $initiative->businessAssets->count() }}
                        </td>
                    </tr>
                @endforeach
            </x-catalog.table>
        </x-catalog.section>

        {{-- KPIs --}}
        <x-catalog.section
            :title="__('KPIs')"
            :count="$businessObjective->kpis->count()"
            :empty-message="__('No KPIs associated with this business objective.')"
            :empty="$businessObjective->kpis->isEmpty()"
        >
            <x-catalog.table :columns="[__('Name'), __('Description')]">
                @foreach ($businessObjective->kpis as $kpi)
                    <tr class="hover:bg-gray-50 dark:hover:bg-zinc-700">
                        <td class="whitespace-nowrap px-6 py-4 text-sm">
                            <a href="{{ route('web.kpis.show', $kpi) }}" class="text-gray-900 hover:text-blue-600 dark:text-gray-100 dark:hover:text-blue-400">
                                {{ $kpi->name }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ Str::limit($kpi->description ?? '-', 50) }}
                        </td>
                    </tr>
                @endforeach
            </x-catalog.table>
        </x-catalog.section>

        <x-slot:meta>
            <x-catalog.timestamps :model="$businessObjective" />
        </x-slot:meta>
    </x-catalog.show-layout>
</x-layouts::app>
