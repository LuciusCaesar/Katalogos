<x-layouts::app :title="$dataIssue->name">
    <x-catalog.show-layout
        :title="$dataIssue->name"
        type-label="__('Data Issue')"
        :breadcrumbs="[
            ['label' => __('Data Issues'), 'href' => route('web.data-issues.index')],
            ['label' => $dataIssue->name, 'href' => null],
        ]"
        :definition="$dataIssue->description"
        :edit-url="route('web.data-issues.edit', $dataIssue)"
        :delete-url="route('web.data-issues.destroy', $dataIssue)"
        :delete-message="__('Are you sure you want to delete this data issue?')"
        :diagram-url="route('web.impact-diagram.show', ['entityType' => 'data-issue', 'entity' => $dataIssue])"
        :back-url="route('web.data-issues.index')"
        back-label="__('Back to Data Issues')"
    >
        {{-- Business Assets --}}
        <x-catalog.section
            :title="__('Business Assets')"
            :count="$dataIssue->businessAssets->count()"
            :empty-message="__('No business assets associated with this data issue.')"
            :empty="$dataIssue->businessAssets->isEmpty()"
        >
            <x-catalog.table :columns="[__('Name'), __('Definition'), __('Domain'), __('Data Initiative')]">
                @foreach ($dataIssue->businessAssets as $asset)
                    <tr class="hover:bg-gray-50 dark:hover:bg-zinc-700">
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                            <a href="{{ route('web.business-assets.show', $asset) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                {{ $asset->name }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $asset->definition ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $asset->domain?->name ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $asset->dataInitiative?->label ?? '-' }}
                        </td>
                    </tr>
                @endforeach
            </x-catalog.table>
        </x-catalog.section>

        {{-- Business Rules --}}
        <x-catalog.section
            :title="__('Business Rules')"
            :count="$dataIssue->businessRules->count()"
            :empty-message="__('No business rules associated with this data issue.')"
            :empty="$dataIssue->businessRules->isEmpty()"
        >
            <x-catalog.table :columns="[__('Name'), __('Description')]">
                @foreach ($dataIssue->businessRules as $rule)
                    <tr class="hover:bg-gray-50 dark:hover:bg-zinc-700">
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                            <a href="{{ route('web.business-rules.show', $rule) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                {{ $rule->name }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $rule->description ?? '-' }}
                        </td>
                    </tr>
                @endforeach
            </x-catalog.table>
        </x-catalog.section>

        <x-slot:meta>
            <x-catalog.timestamps :model="$dataIssue" />
        </x-slot:meta>
    </x-catalog.show-layout>
</x-layouts::app>
