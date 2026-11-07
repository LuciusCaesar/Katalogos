<x-layouts::app :title="__('Create Root Cause')">
    <x-catalog.form-layout
        :title="__('Create Root Cause')"
        :breadcrumbs="[
            ['label' => __('Root Causes'), 'href' => route('web.root-causes.index')],
            ['label' => __('Create Root Cause'), 'href' => null],
        ]"
        :back-url="route('web.root-causes.index')"
        back-label="__('Back to Root Causes')"
    >
        <x-root-causes.form :dataIssues="$dataIssues" />
    </x-catalog.form-layout>
</x-layouts::app>
