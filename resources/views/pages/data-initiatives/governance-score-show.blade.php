<x-layouts::app :title="__('Governance Score Details')">
    <div class="space-y-6">
        <!-- Header Section -->
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                    {{ __('Governance Score Details') }}
                </h1>
                <p class="mt-2 text-gray-600 dark:text-gray-400">
                    {{ $dataInitiative->label }} ({{ $dataInitiative->code }})
                </p>
            </div>
        </div>

        <!-- Breadcrumb -->
        <nav class="text-sm text-gray-500 dark:text-gray-400">
            <a href="{{ route('web.data-initiatives.index') }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                {{ __('Data Initiatives') }}
            </a>
            <span class="mx-2">/</span>
            <a href="{{ route('web.data-initiatives.show', $dataInitiative) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                {{ $dataInitiative->label }}
            </a>
            <span class="mx-2">/</span>
            <a href="{{ route('web.data-initiatives.governance-score.history', $dataInitiative) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                {{ __('Governance Score History') }}
            </a>
            <span class="mx-2">/</span>
            <span class="text-gray-900 dark:text-gray-100">{{ __('Details') }}</span>
        </nav>

        <!-- Score Details Card -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                {{ __('Score Information') }}
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Score') }}</dt>
                            <dd class="text-3xl font-bold @if($history->score >= 0.8) text-green-600 dark:text-green-400
                                @elseif($history->score >= 0.5) text-yellow-600 dark:text-yellow-400
                                @else text-red-600 dark:text-red-400 @endif">
                                {{ number_format($history->score * 100, 1) }}%
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Event') }}</dt>
                            <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $history->event ?? '-' }}</dd>
                        </div>
                    </dl>
                </div>
                <div>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Calculated At') }}</dt>
                            <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $history->calculated_at?->format('M d, Y H:i') ?? $history->created_at->format('M d, Y H:i') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Recorded At') }}</dt>
                            <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $history->created_at->format('M d, Y H:i') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Back Link -->
        <div>
            <a href="{{ route('web.data-initiatives.governance-score.history', $dataInitiative) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                {{ __('Back to Governance Score History') }}
            </a>
        </div>
    </div>
</x-layouts::app>
