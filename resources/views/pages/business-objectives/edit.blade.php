<x-layouts::app :title="__('Edit Business Objective')">
    <x-catalog.form-layout
        :title="__('Edit Business Objective') . ': ' . $businessObjective->name"
        :breadcrumbs="[
            ['label' => __('Business Objectives'), 'href' => route('web.business-objectives.index')],
            ['label' => $businessObjective->name, 'href' => route('web.business-objectives.show', $businessObjective)],
            ['label' => __('Edit'), 'href' => null],
        ]"
        :back-url="route('web.business-objectives.show', $businessObjective)"
        back-label="__('Back to Business Objective')"
    >
        <x-business-objectives.form :businessObjective="$businessObjective" />
    </x-catalog.form-layout>
</x-layouts::app>
