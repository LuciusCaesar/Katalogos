<x-layouts::app :title="__('Edit Domain')">
    <x-catalog.form-layout
        :title="__('Edit Domain') . ': ' . $domain->name"
        :breadcrumbs="[
            ['label' => __('Domains'), 'href' => route('web.domains.index')],
            ['label' => $domain->name, 'href' => route('web.domains.show', $domain)],
            ['label' => __('Edit'), 'href' => null],
        ]"
        :back-url="route('web.domains.show', $domain)"
        back-label="__('Back to Domain')"
    >
        <x-domains.form :domain="$domain" />
    </x-catalog.form-layout>
</x-layouts::app>
