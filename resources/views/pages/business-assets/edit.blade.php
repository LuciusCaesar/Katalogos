<x-layouts::app :title="__('Edit Business Asset')">
    <x-catalog.form-layout
        :title="__('Edit Business Asset') . ': ' . $businessAsset->name"
        :breadcrumbs="[
            ['label' => __('Business Assets'), 'href' => route('web.business-assets.index')],
            ['label' => $businessAsset->name, 'href' => route('web.business-assets.show', $businessAsset)],
            ['label' => __('Edit'), 'href' => null],
        ]"
        :back-url="route('web.business-assets.show', $businessAsset)"
        back-label="__('Back to Business Asset')"
    >
        <x-business-assets.form :businessAsset="$businessAsset" :domains="$domains" :dataInitiatives="$dataInitiatives" />
    </x-catalog.form-layout>
</x-layouts::app>
