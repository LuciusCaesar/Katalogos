<x-layouts::app :title="$businessProcess->name">
    <x-catalog.show-layout
        :title="$businessProcess->name"
        type-label="__('Business Process')"
        :breadcrumbs="[
            ['label' => __('Business Processes'), 'href' => route('web.business-processes.index')],
            ['label' => $businessProcess->name, 'href' => null],
        ]"
        :definition="$businessProcess->description"
        :edit-url="route('web.business-processes.edit', $businessProcess)"
        :delete-url="route('web.business-processes.destroy', $businessProcess)"
        :delete-message="__('Are you sure you want to delete this business process?')"
        :diagram-url="route('web.impact-diagram.show', ['entityType' => 'business-process', 'entity' => $businessProcess])"
        :back-url="route('web.business-processes.index')"
        back-label="__('Back to Business Processes')"
    >
        {{-- Business Assets --}}
        <x-catalog.section
            :title="__('Business Assets')"
            :count="$businessProcess->businessAssets->count()"
            :empty-message="__('No business assets associated.')"
            :empty="$businessProcess->businessAssets->isEmpty()"
        >
            <div class="px-6 py-4">
                <ul class="list-inside list-disc space-y-2 text-sm">
                    @foreach ($businessProcess->businessAssets as $businessAsset)
                        <li>
                            <a href="{{ route('web.business-assets.show', $businessAsset) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                {{ $businessAsset->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </x-catalog.section>

        <x-slot:meta>
            <x-catalog.timestamps :model="$businessProcess" />
        </x-slot:meta>
    </x-catalog.show-layout>
</x-layouts::app>
