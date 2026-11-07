<x-layouts::app :title="__('Create Solution')">
    <x-catalog.form-layout
        :title="__('Create Solution')"
        :breadcrumbs="[
            ['label' => __('Solutions'), 'href' => route('web.solutions.index')],
            ['label' => __('Create Solution'), 'href' => null],
        ]"
        :back-url="route('web.solutions.index')"
        back-label="__('Back to Solutions')"
    >
        <x-solutions.form :rootCauses="$rootCauses" />
    </x-catalog.form-layout>
</x-layouts::app>
