<x-layouts::app :title="__('Edit Root Cause')">
    <x-catalog.form-layout
        :title="__('Edit Root Cause') . ': ' . $rootCause->name"
        :breadcrumbs="[
            ['label' => __('Root Causes'), 'href' => route('web.root-causes.index')],
            ['label' => $rootCause->name, 'href' => route('web.root-causes.show', $rootCause)],
            ['label' => __('Edit'), 'href' => null],
        ]"
        :back-url="route('web.root-causes.show', $rootCause)"
        back-label="__('Back to Root Cause')"
    >
        <x-root-causes.form :rootCause="$rootCause" :dataIssues="$dataIssues" />
    </x-catalog.form-layout>
</x-layouts::app>
