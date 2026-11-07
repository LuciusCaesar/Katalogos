<x-layouts::app :title="__('Edit Data Issue')">
    <x-catalog.form-layout
        :title="__('Edit Data Issue') . ': ' . $dataIssue->name"
        :breadcrumbs="[
            ['label' => __('Data Issues'), 'href' => route('web.data-issues.index')],
            ['label' => $dataIssue->name, 'href' => route('web.data-issues.show', $dataIssue)],
            ['label' => __('Edit'), 'href' => null],
        ]"
        :back-url="route('web.data-issues.show', $dataIssue)"
        back-label="__('Back to Data Issue')"
    >
        <x-data-issues.form :dataIssue="$dataIssue" :businessAssets="$businessAssets" :businessRules="$businessRules" />
    </x-catalog.form-layout>
</x-layouts::app>
