<x-layouts::app :title="$businessAsset->name">
    <div class="space-y-6">
        <!-- Header Section with Action Buttons -->
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                    {{ $businessAsset->name }}
                </h1>
                <p class="mt-2 text-gray-600 dark:text-gray-400">
                    {{ $businessAsset->definition ?? '-' }}
                </p>
            </div>
            <div class="flex space-x-2">
                <a 
                    href="{{ route('web.business-assets.edit', $businessAsset) }}"
                    class="inline-flex items-center px-4 py-2 bg-yellow-600 dark:bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-900 uppercase tracking-widest hover:bg-yellow-700 dark:hover:bg-yellow-600 focus:bg-yellow-700 dark:focus:bg-yellow-600 active:bg-yellow-800 dark:active:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-900 transition ease-in-out duration-150"
                >
                    {{ __('Edit') }}
                </a>
                <form method="POST" action="{{ route('web.business-assets.destroy', $businessAsset) }}" onsubmit="return confirm('{{ __('Are you sure you want to delete this business asset?') }}')">
                    @csrf
                    @method('DELETE')
                    <button 
                        type="submit"
                        class="inline-flex items-center px-4 py-2 bg-red-600 dark:bg-red-500 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-900 uppercase tracking-widest hover:bg-red-700 dark:hover:bg-red-600 focus:bg-red-700 dark:focus:bg-red-600 active:bg-red-800 dark:active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-900 transition ease-in-out duration-150"
                    >
                        {{ __('Delete') }}
                    </button>
                </form>
            </div>
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

        <!-- Back Link -->
        <div>
            <a href="{{ route('web.business-assets.index') }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                {{ __('Back to Business Assets') }}
            </a>
        </div>
    </div>
</x-layouts::app>
