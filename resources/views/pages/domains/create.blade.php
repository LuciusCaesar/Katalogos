<x-layouts::app :title="__('Create Domain')">
    <x-catalog.form-layout
        :title="__('Create Domain')"
        :breadcrumbs="[
            ['label' => __('Domains'), 'href' => route('web.domains.index')],
            ['label' => __('Create Domain'), 'href' => null],
        ]"
        :back-url="route('web.domains.index')"
        back-label="__('Back to Domains')"
    >
        <x-domains.form />
    </x-catalog.form-layout>
</x-layouts::app>
