<x-layouts::app :title="__('Create Data Source')">
    <x-catalog.form-layout
        :title="__('Create Data Source')"
        :breadcrumbs="[
            ['label' => __('Data Sources'), 'href' => route('web.data-sources.index')],
            ['label' => __('Create Data Source'), 'href' => null],
        ]"
        :back-url="route('web.data-sources.index')"
        back-label="__('Back to Data Sources')"
    >
        <x-data-sources.form :businessAssets="$businessAssets" />
    </x-catalog.form-layout>
</x-layouts::app>
