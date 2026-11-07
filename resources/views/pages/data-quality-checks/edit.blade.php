<x-layouts::app :title="__('Edit Data Quality Check')">
    <x-catalog.form-layout
        :title="__('Edit Data Quality Check') . ': ' . $dataQualityCheck->name"
        :breadcrumbs="[
            ['label' => __('Data Quality Checks'), 'href' => route('web.data-quality-checks.index')],
            ['label' => $dataQualityCheck->name, 'href' => route('web.data-quality-checks.show', $dataQualityCheck)],
            ['label' => __('Edit'), 'href' => null],
        ]"
        :back-url="route('web.data-quality-checks.show', $dataQualityCheck)"
        back-label="__('Back to Data Quality Check')"
    >
        <x-data-quality-checks.form :dataQualityCheck="$dataQualityCheck" :businessRules="$businessRules" :dataSources="$dataSources" />
    </x-catalog.form-layout>
</x-layouts::app>
