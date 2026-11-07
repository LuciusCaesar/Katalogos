<x-layouts::app :title="__('Edit Business Process')">
    <x-catalog.form-layout
        :title="__('Edit Business Process') . ': ' . $businessProcess->name"
        :breadcrumbs="[
            ['label' => __('Business Processes'), 'href' => route('web.business-processes.index')],
            ['label' => $businessProcess->name, 'href' => route('web.business-processes.show', $businessProcess)],
            ['label' => __('Edit'), 'href' => null],
        ]"
        :back-url="route('web.business-processes.show', $businessProcess)"
        back-label="__('Back to Business Process')"
    >
        <x-business-processes.form :businessProcess="$businessProcess" :businessAssets="$businessAssets" />
    </x-catalog.form-layout>
</x-layouts::app>
