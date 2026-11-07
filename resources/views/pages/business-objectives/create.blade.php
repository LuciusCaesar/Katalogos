<x-layouts::app :title="__('Create Business Objective')">
    <x-catalog.form-layout
        :title="__('Create Business Objective')"
        :breadcrumbs="[
            ['label' => __('Business Objectives'), 'href' => route('web.business-objectives.index')],
            ['label' => __('Create Business Objective'), 'href' => null],
        ]"
        :back-url="route('web.business-objectives.index')"
        back-label="__('Back to Business Objectives')"
    >
        <x-business-objectives.form />
    </x-catalog.form-layout>
</x-layouts::app>
