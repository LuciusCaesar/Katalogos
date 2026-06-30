<x-layouts::app :title="__('Edit Business Objective')">
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                {{ __('Edit Business Objective') }}: {{ $businessObjective->name }}
            </h1>
        </div>

        <!-- Form -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-6">
            <x-business-objectives.form :businessObjective="$businessObjective" />
        </div>

        <!-- Back Link -->
        <div>
            <a href="{{ route('web.business-objectives.index') }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                {{ __('Back to Business Objectives') }}
            </a>
        </div>
    </div>
</x-layouts::app>
