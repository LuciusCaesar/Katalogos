<x-layouts::app :title="__('Create Business Rule')">
    <x-catalog.form-layout
        :title="__('Create Business Rule')"
        :breadcrumbs="[
            ['label' => __('Business Rules'), 'href' => route('web.business-rules.index')],
            ['label' => __('Create Business Rule'), 'href' => null],
        ]"
        :back-url="route('web.business-rules.index')"
        back-label="__('Back to Business Rules')"
    >
        <x-business-rules.form :businessAssets="$businessAssets" :dataIssues="$dataIssues" />
    </x-catalog.form-layout>
</x-layouts::app>
