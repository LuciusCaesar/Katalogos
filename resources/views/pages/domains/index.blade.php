<x-layouts::app :title="__('Domains')">
    <div class="space-y-6">
        <!-- Header with New Button -->
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                {{ __('Domains') }}
            </h1>
            <a 
                href="{{ route('web.domains.create') }}"
                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
            >
                {{ __('New') }}
            </a>
        </div>

        @if (session('success'))
            <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-md p-4">
                <p class="text-green-800 dark:text-green-200 text-sm">{{ session('success') }}</p>
            </div>
        @endif

        @if ($domains->isEmpty())
            <!-- Empty State -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-8 text-center">
                <p class="text-gray-500 dark:text-gray-400">
                    {{ __('No domains found. Create one to get started.') }}
                </p>
            </div>
        @else
            <!-- Domains Table -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-neutral-200 dark:border-neutral-700 overflow-hidden">
                <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-zinc-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('Name') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('Description') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('Business Terms') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('Created At') }}
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('Actions') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-zinc-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($domains as $domain)
                            <tr class="hover:bg-gray-50 dark:hover:bg-zinc-700">
                                <!-- Name -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    <a href="{{ route('web.domains.show', $domain) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                        {{ $domain->name }}
                                    </a>
                                </td>
                                <!-- Description -->
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $domain->description ?? '-' }}
                                </td>
                                <!-- Business Assets Count -->
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    <a href="{{ route('web.domains.show', $domain) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                        {{ $domain->business_assets_count }} {{ __('Business Term', ['count' => $domain->business_assets_count]) }}
                                    </a>
                                </td>
                                <!-- Created At -->
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $domain->created_at->format('Y-m-d H:i') }}
                                </td>
                                <!-- Actions -->
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end space-x-2">
                                        <a 
                                            href="{{ route('web.domains.edit', $domain) }}"
                                            class="px-3 py-1 bg-yellow-600 text-white text-xs rounded hover:bg-yellow-700"
                                            title="{{ __('Edit') }}"
                                        >
                                            {{ __('Edit') }}
                                        </a>
                                        <form method="POST" action="{{ route('web.domains.destroy', $domain) }}" onsubmit="return confirm('{{ __('Are you sure you want to delete this domain?') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button 
                                                type="submit"
                                                class="px-3 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700"
                                                title="{{ __('Delete') }}"
                                            >
                                                {{ __('Delete') }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="pt-4">
                {{ $domains->links() }}
            </div>
        @endif
    </div>
</x-layouts::app>
