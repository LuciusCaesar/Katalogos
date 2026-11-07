<x-layouts::app :title="__('Create KPI')">
    <x-catalog.form-layout
        :title="__('Create KPI')"
        :breadcrumbs="[
            ['label' => __('KPIs'), 'href' => route('web.kpis.index')],
            ['label' => __('Create KPI'), 'href' => null],
        ]"
        :back-url="route('web.kpis.index')"
        back-label="__('Back to KPIs')"
    >
        <x-kpis.form :businessObjectives="$businessObjectives" />
    </x-catalog.form-layout>
</x-layouts::app>
