<x-layouts::app :title="$kpi->name">
    <div class="space-y-6">
        <!-- Header Section with Action Buttons -->
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                    {{ $kpi->name }}
                </h1>
                @if($kpi->description)
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        {{ $kpi->description }}
                    </p>
                @endif
            </div>
            <div class="flex space-x-2">
                <a 
                    href="{{ route('web.kpis.edit', $kpi) }}"
                    class="inline-flex items-center px-4 py-2 bg-yellow-600 dark:bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-900 uppercase tracking-widest hover:bg-yellow-700 dark:hover:bg-yellow-600 focus:bg-yellow-700 dark:focus:bg-yellow-600 active:bg-yellow-800 dark:active:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-900 transition ease-in-out duration-150"
                >
                    {{ __('Edit') }}
                </a>
                <form method="POST" action="{{ route('web.kpis.destroy', $kpi) }}" onsubmit="return confirm('{{ __("Are you sure you want to delete this KPI?") }}')">
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
                <!-- Metadata Card -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('Metadata') }}</h3>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Created') }}</dt>
                            <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $kpi->created_at->format('M d, Y H:i') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Last Updated') }}</dt>
                            <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $kpi->updated_at->format('M d, Y H:i') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Right column (1/3 width) - sidebar info -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Business Objective Card -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('Business Objective') }}</h3>
                    @if($kpi->businessObjective)
                        <div class="space-y-3">
                            <p class="text-gray-900 dark:text-gray-100">
                                <a href="{{ route('web.business-objectives.show', $kpi->businessObjective) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                    {{ $kpi->businessObjective->name }}
                                </a>
                            </p>
                            @if($kpi->businessObjective->description)
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ Str::limit($kpi->businessObjective->description, 100) }}
                                </p>
                            @endif
                        </div>
                    @else
                        <p class="text-gray-500 dark:text-gray-400 text-sm">{{ __('Not assigned') }}</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Back Link -->
        <div>
            <a href="{{ route('web.kpis.index') }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                {{ __('Back to KPIs') }}
            </a>
        </div>
    </div>
</x-layouts::app>
