<x-layouts::app :title="__('Create Business Event')">
    <x-catalog.form-layout
        :title="__('Create Business Event')"
        :breadcrumbs="[
            ['label' => __('Business Events'), 'href' => route('web.business-events.index')],
            ['label' => __('Create Business Event'), 'href' => null],
        ]"
        :back-url="route('web.business-events.index')"
        back-label="__('Back to Business Events')"
    >
        <x-business-events.form :businessAssets="$businessAssets" :businessProcesses="$businessProcesses" />
    </x-catalog.form-layout>
</x-layouts::app>
