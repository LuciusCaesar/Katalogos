<x-layouts::app :title="__('Create Business Asset')">
    <x-catalog.form-layout
        :title="__('Create Business Asset')"
        :breadcrumbs="[
            ['label' => __('Business Assets'), 'href' => route('web.business-assets.index')],
            ['label' => __('Create Business Asset'), 'href' => null],
        ]"
        :back-url="route('web.business-assets.index')"
        back-label="__('Back to Business Assets')"
    >
        <x-business-assets.form :domains="$domains" :dataInitiatives="$dataInitiatives" />
    </x-catalog.form-layout>
</x-layouts::app>
