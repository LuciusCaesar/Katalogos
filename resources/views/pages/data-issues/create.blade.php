<x-layouts::app :title="__('Create Data Issue')">
    <x-catalog.form-layout
        :title="__('Create Data Issue')"
        :breadcrumbs="[
            ['label' => __('Data Issues'), 'href' => route('web.data-issues.index')],
            ['label' => __('Create Data Issue'), 'href' => null],
        ]"
        :back-url="route('web.data-issues.index')"
        back-label="__('Back to Data Issues')"
    >
        <x-data-issues.form :businessAssets="$businessAssets" :businessRules="$businessRules" />
    </x-catalog.form-layout>
</x-layouts::app>
