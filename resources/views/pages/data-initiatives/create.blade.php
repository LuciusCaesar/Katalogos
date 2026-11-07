<x-layouts::app :title="__('Create Data Initiative')">
    <x-catalog.form-layout
        :title="__('Create Data Initiative')"
        :breadcrumbs="[
            ['label' => __('Data Initiatives'), 'href' => route('web.data-initiatives.index')],
            ['label' => __('Create Data Initiative'), 'href' => null],
        ]"
        :back-url="route('web.data-initiatives.index')"
        back-label="__('Back to Data Initiatives')"
    >
        <x-data-initiatives.form :businessObjectives="$businessObjectives" />
    </x-catalog.form-layout>
</x-layouts::app>
