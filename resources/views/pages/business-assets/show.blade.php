<x-layouts::app :title="$businessAsset->name">
    <div class="space-y-6">
        <!-- Header Section -->
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                {{ $businessAsset->name }}
            </h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                {{ $businessAsset->definition ?? '-' }}
            </p>
        </div>
        
        <!-- Two-column layout -->
        <div class="grid gap-6 lg:grid-cols-3">
            <!-- Left column (2/3 width) - main content area -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Future content goes here -->
            </div>
            
            <!-- Right column (1/3 width) - info cards -->
            <div class="lg:col-span-1 space-y-4">
                <!-- Domain Card -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-4">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        {{ __('Domain') }}
                    </h3>
                    <p class="mt-2 text-gray-900 dark:text-gray-100">
                        {{ $businessAsset->domain?->name ?? '-' }}
                    </p>
                </div>
                
                <!-- Data Initiative Card -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-4">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        {{ __('Data Initiative') }}
                    </h3>
                    <p class="mt-2 text-gray-900 dark:text-gray-100">
                        {{ $businessAsset->dataInitiative?->label ?? '-' }}
                    </p>
                </div>
                
                <!-- Data Steward Card -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-4">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        {{ __('Data Steward') }}
                    </h3>
                    <p class="mt-2 text-gray-900 dark:text-gray-100">
                        {{ $businessAsset->dataSteward()->first()?->name ?? '-' }}
                    </p>
                </div>
                
                <!-- Data Owner Card -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-4">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        {{ __('Data Owner') }}
                    </h3>
                    <p class="mt-2 text-gray-900 dark:text-gray-100">
                        {{ $businessAsset->dataOwner()->first()?->name ?? '-' }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-layouts::app>
