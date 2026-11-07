<x-layouts::app :title="$businessEvent->name">
    <x-catalog.show-layout
        :title="$businessEvent->name"
        type-label="__('Business Event')"
        :breadcrumbs="[
            ['label' => __('Business Events'), 'href' => route('web.business-events.index')],
            ['label' => $businessEvent->name, 'href' => null],
        ]"
        :definition="$businessEvent->description"
        :edit-url="route('web.business-events.edit', $businessEvent)"
        :delete-url="route('web.business-events.destroy', $businessEvent)"
        :delete-message="__('Are you sure you want to delete this business event?')"
        :diagram-url="route('web.impact-diagram.show', ['entityType' => 'business-event', 'entity' => $businessEvent])"
        :back-url="route('web.business-events.index')"
        back-label="__('Back to Business Events')"
    >
        {{-- Business Assets --}}
        <x-catalog.section
            :title="__('Business Assets')"
            :count="$businessEvent->businessAssets->count()"
            :empty-message="__('No business assets associated.')"
            :empty="$businessEvent->businessAssets->isEmpty()"
        >
            <div class="px-6 py-4">
                <ul class="list-inside list-disc space-y-2 text-sm">
                    @foreach ($businessEvent->businessAssets as $businessAsset)
                        <li>
                            <a href="{{ route('web.business-assets.show', $businessAsset) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                {{ $businessAsset->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </x-catalog.section>

        {{-- Business Processes --}}
        <x-catalog.section
            :title="__('Business Processes')"
            :count="$businessEvent->businessProcesses->count()"
            :empty-message="__('No business processes associated.')"
            :empty="$businessEvent->businessProcesses->isEmpty()"
        >
            <div class="px-6 py-4">
                <ul class="list-inside list-disc space-y-2 text-sm">
                    @foreach ($businessEvent->businessProcesses as $businessProcess)
                        <li>
                            <a href="{{ route('web.business-processes.show', $businessProcess) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                {{ $businessProcess->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </x-catalog.section>

        <x-slot:meta>
            <x-catalog.timestamps :model="$businessEvent" />
        </x-slot:meta>
    </x-catalog.show-layout>
</x-layouts::app>
