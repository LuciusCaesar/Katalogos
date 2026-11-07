<x-layouts::app :title="__('Create Business Process')">
    <x-catalog.form-layout
        :title="__('Create Business Process')"
        :breadcrumbs="[
            ['label' => __('Business Processes'), 'href' => route('web.business-processes.index')],
            ['label' => __('Create Business Process'), 'href' => null],
        ]"
        :back-url="route('web.business-processes.index')"
        back-label="__('Back to Business Processes')"
    >
        <x-business-processes.form :businessAssets="$businessAssets" />
    </x-catalog.form-layout>
</x-layouts::app>
