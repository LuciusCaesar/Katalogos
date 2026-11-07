<x-layouts::app :title="$rootCause->name">
    <x-catalog.show-layout
        :title="$rootCause->name"
        type-label="__('Root Cause')"
        :breadcrumbs="[
            ['label' => __('Root Causes'), 'href' => route('web.root-causes.index')],
            ['label' => $rootCause->name, 'href' => null],
        ]"
        :definition="$rootCause->description"
        :edit-url="route('web.root-causes.edit', $rootCause)"
        :delete-url="route('web.root-causes.destroy', $rootCause)"
        :delete-message="__('Are you sure you want to delete this root cause?')"
        :diagram-url="route('web.impact-diagram.show', ['entityType' => 'root-cause', 'entity' => $rootCause])"
        :back-url="route('web.root-causes.index')"
        back-label="__('Back to Root Causes')"
    >
        <x-slot:badges>
            <flux:badge color="blue">{{ $rootCause->dimension->value }}</flux:badge>
        </x-slot:badges>

        {{-- Data Issues --}}
        <x-catalog.section
            :title="__('Data Issues')"
            :count="$rootCause->dataIssues->count()"
            :empty-message="__('No data issues associated with this root cause.')"
            :empty="$rootCause->dataIssues->isEmpty()"
        >
            <x-catalog.table :columns="[__('Name'), __('Description'), __('Business Assets')]">
                @foreach ($rootCause->dataIssues as $dataIssue)
                    <tr class="hover:bg-gray-50 dark:hover:bg-zinc-700">
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                            <a href="{{ route('web.data-issues.show', $dataIssue) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                {{ $dataIssue->name }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $dataIssue->description ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $dataIssue->business_assets_count }}
                        </td>
                    </tr>
                @endforeach
            </x-catalog.table>
        </x-catalog.section>

        <x-slot:meta>
            <x-catalog.timestamps :model="$rootCause" />
        </x-slot:meta>
    </x-catalog.show-layout>
</x-layouts::app>
