<x-layouts::app :title="__('Edit Data Source')">
    <x-catalog.form-layout
        :title="__('Edit Data Source') . ': ' . $dataSource->name"
        :breadcrumbs="[
            ['label' => __('Data Sources'), 'href' => route('web.data-sources.index')],
            ['label' => $dataSource->name, 'href' => route('web.data-sources.show', $dataSource)],
            ['label' => __('Edit'), 'href' => null],
        ]"
        :back-url="route('web.data-sources.show', $dataSource)"
        back-label="__('Back to Data Source')"
    >
        <x-data-sources.form :dataSource="$dataSource" :businessAssets="$businessAssets" />
    </x-catalog.form-layout>
</x-layouts::app>
