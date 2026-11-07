<x-layouts::app :title="__('Edit Solution')">
    <x-catalog.form-layout
        :title="__('Edit Solution') . ': ' . $solution->name"
        :breadcrumbs="[
            ['label' => __('Solutions'), 'href' => route('web.solutions.index')],
            ['label' => $solution->name, 'href' => route('web.solutions.show', $solution)],
            ['label' => __('Edit'), 'href' => null],
        ]"
        :back-url="route('web.solutions.show', $solution)"
        back-label="__('Back to Solution')"
    >
        <x-solutions.form :solution="$solution" :rootCauses="$rootCauses" />
    </x-catalog.form-layout>
</x-layouts::app>
