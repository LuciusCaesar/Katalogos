<x-layouts::app :title="__('Edit Business Event')">
    <x-catalog.form-layout
        :title="__('Edit Business Event') . ': ' . $businessEvent->name"
        :breadcrumbs="[
            ['label' => __('Business Events'), 'href' => route('web.business-events.index')],
            ['label' => $businessEvent->name, 'href' => route('web.business-events.show', $businessEvent)],
            ['label' => __('Edit'), 'href' => null],
        ]"
        :back-url="route('web.business-events.show', $businessEvent)"
        back-label="__('Back to Business Event')"
    >
        <x-business-events.form :businessEvent="$businessEvent" :businessAssets="$businessAssets" :businessProcesses="$businessProcesses" />
    </x-catalog.form-layout>
</x-layouts::app>
