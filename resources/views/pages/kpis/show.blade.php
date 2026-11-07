<x-layouts::app :title="$kpi->name">
    <x-catalog.show-layout
        :title="$kpi->name"
        type-label="__('KPI')"
        :breadcrumbs="[
            ['label' => __('KPIs'), 'href' => route('web.kpis.index')],
            ['label' => $kpi->name, 'href' => null],
        ]"
        :definition="$kpi->description"
        :edit-url="route('web.kpis.edit', $kpi)"
        :delete-url="route('web.kpis.destroy', $kpi)"
        :delete-message="__('Are you sure you want to delete this KPI?')"
        :back-url="route('web.kpis.index')"
        back-label="__('Back to KPIs')"
    >
        <x-catalog.detail-card :title="__('Business Objective')">
            @if ($kpi->businessObjective)
                <div class="space-y-2">
                    <a href="{{ route('web.business-objectives.show', $kpi->businessObjective) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                        {{ $kpi->businessObjective->name }}
                    </a>
                    @if ($kpi->businessObjective->description)
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ Str::limit($kpi->businessObjective->description, 100) }}
                        </p>
                    @endif
                </div>
            @else
                <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('Not assigned') }}</span>
            @endif
        </x-catalog.detail-card>

        <x-slot:meta>
            <x-catalog.timestamps :model="$kpi" />
        </x-slot:meta>
    </x-catalog.show-layout>
</x-layouts::app>
