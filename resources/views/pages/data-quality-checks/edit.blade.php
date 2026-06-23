<x-layouts::app :title="__('Edit Data Quality Check')">
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                {{ __('Edit Data Quality Check') }}
            </h1>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-6">
            <x-data-quality-checks.form :dataQualityCheck="$dataQualityCheck" :businessRules="$businessRules" :dataSources="$dataSources" />
        </div>

        <div class="mt-4">
            <a href="{{ route('web.data-quality-checks.index') }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                {{ __('Back to Data Quality Checks') }}
            </a>
        </div>
    </div>
</x-layouts::app>
