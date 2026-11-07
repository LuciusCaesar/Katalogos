<x-layouts::app :title="__('Edit KPI')">
    <x-catalog.form-layout
        :title="__('Edit KPI') . ': ' . $kpi->name"
        :breadcrumbs="[
            ['label' => __('KPIs'), 'href' => route('web.kpis.index')],
            ['label' => $kpi->name, 'href' => route('web.kpis.show', $kpi)],
            ['label' => __('Edit'), 'href' => null],
        ]"
        :back-url="route('web.kpis.show', $kpi)"
        back-label="__('Back to KPI')"
    >
        <x-kpis.form :kpi="$kpi" :businessObjectives="$businessObjectives" />
    </x-catalog.form-layout>
</x-layouts::app>
