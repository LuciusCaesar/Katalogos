<x-layouts::app :title="__('Create Data Quality Check')">
    <x-catalog.form-layout
        :title="__('Create Data Quality Check')"
        :breadcrumbs="[
            ['label' => __('Data Quality Checks'), 'href' => route('web.data-quality-checks.index')],
            ['label' => __('Create Data Quality Check'), 'href' => null],
        ]"
        :back-url="route('web.data-quality-checks.index')"
        back-label="__('Back to Data Quality Checks')"
    >
        <x-data-quality-checks.form :businessRules="$businessRules" :dataSources="$dataSources" />
    </x-catalog.form-layout>
</x-layouts::app>
