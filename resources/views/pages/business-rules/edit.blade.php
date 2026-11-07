<x-layouts::app :title="__('Edit Business Rule')">
    <x-catalog.form-layout
        :title="__('Edit Business Rule') . ': ' . $businessRule->name"
        :breadcrumbs="[
            ['label' => __('Business Rules'), 'href' => route('web.business-rules.index')],
            ['label' => $businessRule->name, 'href' => route('web.business-rules.show', $businessRule)],
            ['label' => __('Edit'), 'href' => null],
        ]"
        :back-url="route('web.business-rules.show', $businessRule)"
        back-label="__('Back to Business Rule')"
    >
        <x-business-rules.form :businessRule="$businessRule" :businessAssets="$businessAssets" :dataIssues="$dataIssues" />
    </x-catalog.form-layout>
</x-layouts::app>
