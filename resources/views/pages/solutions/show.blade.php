<x-layouts::app :title="$solution->name">
    <x-catalog.show-layout
        :title="$solution->name"
        type-label="__('Solution')"
        :breadcrumbs="[
            ['label' => __('Solutions'), 'href' => route('web.solutions.index')],
            ['label' => $solution->name, 'href' => null],
        ]"
        :definition="$solution->description"
        :edit-url="route('web.solutions.edit', $solution)"
        :delete-url="route('web.solutions.destroy', $solution)"
        :delete-message="__('Are you sure you want to delete this solution?')"
        :diagram-url="route('web.impact-diagram.show', ['entityType' => 'solution', 'entity' => $solution])"
        :back-url="route('web.solutions.index')"
        back-label="__('Back to Solutions')"
    >
        <x-slot:badges>
            <flux:badge color="blue">{{ $solution->dimension->value }}</flux:badge>
            @if ($solution->implementation_status)
                <flux:badge color="purple">{{ $solution->implementation_status->value }}</flux:badge>
            @endif
        </x-slot:badges>

        {{-- Root Causes --}}
        <x-catalog.section
            :title="__('Root Causes')"
            :count="$solution->rootCauses->count()"
            :empty-message="__('No root causes associated with this solution.')"
            :empty="$solution->rootCauses->isEmpty()"
        >
            <x-catalog.table :columns="[__('Name'), __('Description'), __('Dimension'), __('Data Issues')]">
                @foreach ($solution->rootCauses as $rootCause)
                    <tr class="hover:bg-gray-50 dark:hover:bg-zinc-700">
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                            <a href="{{ route('web.root-causes.show', $rootCause) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                {{ $rootCause->name }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $rootCause->description ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $rootCause->dimension->value }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $rootCause->data_issues_count }}
                        </td>
                    </tr>
                @endforeach
            </x-catalog.table>
        </x-catalog.section>

        <x-slot:meta>
            <x-catalog.timestamps :model="$solution" />
        </x-slot:meta>
    </x-catalog.show-layout>
</x-layouts::app>
