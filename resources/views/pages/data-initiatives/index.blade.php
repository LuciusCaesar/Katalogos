<x-layouts::app :title="__('Data Initiatives')">
    <div class="space-y-6">
        <!-- Header with New Button -->
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                {{ __('Data Initiatives') }}
            </h1>
            <a 
                href="{{ route('web.data-initiatives.create') }}"
                class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-900 uppercase tracking-widest hover:bg-blue-700 dark:hover:bg-blue-600 focus:bg-blue-700 dark:focus:bg-blue-600 active:bg-blue-800 dark:active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-900 transition ease-in-out duration-150"
            >
                {{ __('New') }}
            </a>
        </div>

        @if (session('success'))
            <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-md p-4">
                <p class="text-green-800 dark:text-green-200 text-sm">{{ session('success') }}</p>
            </div>
        @endif

        @if ($dataInitiatives->isEmpty())
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-8 text-center">
                <p class="text-gray-500 dark:text-gray-400">
                    {{ __('No data initiatives found. Create one to get started.') }}
                </p>
            </div>
        @else
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-neutral-200 dark:border-neutral-700 overflow-hidden">
                <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-zinc-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('Code') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('Label') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('Description') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('Business Assets') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('Data Steward') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('Data Owner') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('Avg Governance Score') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-zinc-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($dataInitiatives as $initiative)
                            <tr class="hover:bg-gray-50 dark:hover:bg-zinc-700">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    <a href="{{ route('web.data-initiatives.show', $initiative) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                        {{ $initiative->code }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $initiative->label }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    {{ Str::limit($initiative->description ?? '-', 50) }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $initiative->businessAssets->count() }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $initiative->dataSteward()->first()?->name ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $initiative->dataOwner()->first()?->name ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    @if($initiative->average_governance_score)
                                        <span class="@if($initiative->average_governance_score >= 0.8) text-green-600 dark:text-green-400
                                            @elseif($initiative->average_governance_score >= 0.5) text-yellow-600 dark:text-yellow-400
                                            @else text-red-600 dark:text-red-400 @endif font-medium">
                                            {{ number_format($initiative->average_governance_score * 100, 1) }}%
                                        </span>
                                    @else
                                        <span class="text-gray-400 dark:text-gray-500">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="pt-4">
                {{ $dataInitiatives->links() }}
            </div>
        @endif
    </div>
</x-layouts::app>
