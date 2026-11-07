<x-layouts::app :title="__('Create Regulation')">
    <x-catalog.form-layout
        :title="__('Create Regulation')"
        :breadcrumbs="[
            ['label' => __('Regulations'), 'href' => route('web.regulations.index')],
            ['label' => __('Create Regulation'), 'href' => null],
        ]"
        :back-url="route('web.regulations.index')"
        back-label="__('Back to Regulations')"
    >
        <x-regulations.form :regulation="null" :businessAssets="$businessAssets" />
    </x-catalog.form-layout>
</x-layouts::app>
