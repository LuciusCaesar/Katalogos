<x-layouts::app :title="$businessObjective->name">
    <div class="space-y-6">
        <!-- Header Section with Action Buttons -->
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                    {{ $businessObjective->name }}
                </h1>
                @if($businessObjective->description)
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        {{ $businessObjective->description }}
                    </p>
                @endif
            </div>
            <div class="flex space-x-2">
                <a 
                    href="{{ route('web.business-objectives.edit', $businessObjective) }}"
                    class="inline-flex items-center px-4 py-2 bg-yellow-600 dark:bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-900 uppercase tracking-widest hover:bg-yellow-700 dark:hover:bg-yellow-600 focus:bg-yellow-700 dark:focus:bg-yellow-600 active:bg-yellow-800 dark:active:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-900 transition ease-in-out duration-150"
                >
                    {{ __('Edit') }}
                </a>
                <form method="POST" action="{{ route('web.business-objectives.destroy', $businessObjective) }}" onsubmit="return confirm('{{ __("Are you sure you want to delete this business objective?") }}')">
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
                <!-- Data Initiatives Section -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-neutral-200 dark:border-neutral-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-zinc-700">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            {{ __('Data Initiatives') }} ({{ $businessObjective->dataInitiatives->count() }})
                        </h2>
                    </div>
                    @if ($businessObjective->dataInitiatives->isEmpty())
                        <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                            {{ __('No data initiatives associated with this business objective.') }}
                        </div>
                    @else
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
                                        {{ __('Business Assets') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-zinc-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($businessObjective->dataInitiatives as $initiative)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-zinc-700">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <a href="{{ route('web.data-initiatives.show', $initiative) }}" 
                                               class="text-gray-900 dark:text-gray-100 hover:text-blue-600 dark:hover:text-blue-400">
                                                {{ $initiative->code }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            {{ $initiative->label }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            {{ $initiative->businessAssets->count() }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>

            <!-- Right column (1/3 width) - sidebar info -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Metadata Card -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('Metadata') }}</h3>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Created') }}</dt>
                            <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $businessObjective->created_at->format('M d, Y H:i') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Last Updated') }}</dt>
                            <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $businessObjective->updated_at->format('M d, Y H:i') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Back Link -->
        <div>
            <a href="{{ route('web.business-objectives.index') }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                {{ __('Back to Business Objectives') }}
            </a>
        </div>
    </div>
</x-layouts::app>
