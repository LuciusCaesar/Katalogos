<x-layouts::app :title="__('Governance Score History')">
    <div class="space-y-6">
        <!-- Header Section -->
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                    {{ __('Governance Score History') }}
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
            <span class="text-gray-900 dark:text-gray-100">{{ __('Governance Score History') }}</span>
        </nav>

        @if($governanceHistory !== [])
            <!-- Current Metrics Card -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                    {{ __('Current Metrics') }}
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-gray-50 dark:bg-zinc-700 rounded-lg p-4 text-center">
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Average Score') }}</p>
                        <p class="text-2xl font-bold @if($governanceMetrics['average'] >= 0.8) text-green-600 dark:text-green-400
                                @elseif($governanceMetrics['average'] >= 0.5) text-yellow-600 dark:text-yellow-400
                                @else text-red-600 dark:text-red-400 @endif">
                            {{ $governanceMetrics['average'] !== null ? number_format($governanceMetrics['average'] * 100, 1) . '%' : '-' }}
                        </p>
                    </div>
                    <div class="bg-gray-50 dark:bg-zinc-700 rounded-lg p-4 text-center">
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Total Points') }}</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                            {{ number_format($governanceMetrics['total_points'], 1) }} / {{ number_format($governanceMetrics['max_possible_points'], 1) }}
                        </p>
                    </div>
                </div>
                <p class="mt-3 text-xs text-gray-500 dark:text-gray-400">
                    {{ __('Computed from the governance scores of :scored business assets (out of :total).', ['scored' => $governanceMetrics['scored_assets'], 'total' => $governanceMetrics['total_assets']]) }}
                </p>
            </div>

            <!-- History Table -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-neutral-200 dark:border-neutral-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-zinc-700">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        {{ __('Score History') }} ({{ count($governanceHistory) }})
                    </h2>
                </div>
                <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-zinc-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('Date') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('Average') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('Total Points') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('Scored Assets') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('Event') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-zinc-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($governanceHistory as $point)
                            <tr class="hover:bg-gray-50 dark:hover:bg-zinc-700">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $point['calculated_at']->format('M d, Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($point['average'] !== null)
                                        <span class="@if($point['average'] >= 0.8) text-green-600 dark:text-green-400
                                            @elseif($point['average'] >= 0.5) text-yellow-600 dark:text-yellow-400
                                            @else text-red-600 dark:text-red-400 @endif font-medium">
                                            {{ number_format($point['average'] * 100, 1) }}%
                                        </span>
                                    @else
                                        <span class="text-gray-400 dark:text-gray-500">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    {{ number_format($point['total_points'], 1) }} / {{ number_format($point['max_possible_points'], 1) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $point['scored_assets'] }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $point['event'] }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-8 text-center">
                <p class="text-gray-500 dark:text-gray-400">
                    {{ __('No governance score history available for this data initiative.') }}
                </p>
            </div>
        @endif

        <!-- Back Link -->
        <div>
            <a href="{{ route('web.data-initiatives.show', $dataInitiative) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                {{ __('Back to Data Initiative') }}
            </a>
        </div>
    </div>
</x-layouts::app>
