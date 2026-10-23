<x-layouts::app :title="__('Edit Business Event')">
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                {{ __('Edit Business Event') }}
            </h1>
        </div>

        <!-- Form -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-6">
            <x-business-events.form :businessEvent="$businessEvent" :businessAssets="$businessAssets" :businessProcesses="$businessProcesses" />
        </div>

        <!-- Back Link -->
        <div>
            <a href="{{ route('web.business-events.index') }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                {{ __('Back to Business Events') }}
            </a>
        </div>
    </div>
</x-layouts::app>
