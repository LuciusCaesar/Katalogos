<x-layouts::app :title="__('Edit Regulation')">
    <x-catalog.form-layout
        :title="__('Edit Regulation') . ': ' . $regulation->name"
        :breadcrumbs="[
            ['label' => __('Regulations'), 'href' => route('web.regulations.index')],
            ['label' => $regulation->name, 'href' => route('web.regulations.show', $regulation)],
            ['label' => __('Edit'), 'href' => null],
        ]"
        :back-url="route('web.regulations.show', $regulation)"
        back-label="__('Back to Regulation')"
    >
        <x-regulations.form :regulation="$regulation" :businessAssets="$businessAssets" />
    </x-catalog.form-layout>
</x-layouts::app>
