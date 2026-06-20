<x-layouts::app :title="__('Edit Solution')">
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                {{ __('Edit Solution') }}
            </h1>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-6">
            <x-solutions.form :solution="$solution" :rootCauses="$rootCauses" />
        </div>

        <div class="pt-4">
            <a href="{{ route('web.solutions.index') }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                {{ __('Back to Solutions') }}
            </a>
        </div>
    </div>
</x-layouts::app>
