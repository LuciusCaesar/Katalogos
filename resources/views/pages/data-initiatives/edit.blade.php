<x-layouts::app :title="__('Edit Data Initiative')">
    <x-catalog.form-layout
        :title="__('Edit Data Initiative') . ': ' . $dataInitiative->label"
        :breadcrumbs="[
            ['label' => __('Data Initiatives'), 'href' => route('web.data-initiatives.index')],
            ['label' => $dataInitiative->label, 'href' => route('web.data-initiatives.show', $dataInitiative)],
            ['label' => __('Edit'), 'href' => null],
        ]"
        :back-url="route('web.data-initiatives.show', $dataInitiative)"
        back-label="__('Back to Data Initiative')"
    >
        <x-data-initiatives.form
            :dataInitiative="$dataInitiative"
            :businessObjectives="$businessObjectives"
            :selectedBusinessObjectiveIds="$selectedBusinessObjectiveIds"
        />
    </x-catalog.form-layout>
</x-layouts::app>
