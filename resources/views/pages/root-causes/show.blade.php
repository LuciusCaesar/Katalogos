<x-layouts::app :title="$rootCause->name">
    <div class="space-y-6">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                    {{ $rootCause->name }}
                </h1>
                <p class="mt-2 text-gray-600 dark:text-gray-400">
                    <span class="inline-block px-2 py-1 bg-blue-100 dark:bg-blue-900/30 rounded text-blue-800 dark:text-blue-200 text-xs">
                        {{ $rootCause->dimension->value }}
                    </span>
                </p>
                @if ($rootCause->description)
                    <p class="mt-4 text-gray-600 dark:text-gray-400">
                        {{ $rootCause->description }}
                    </p>
                @endif
            </div>
            <div class="flex space-x-2">
                <a 
                    href="{{ route('web.root-causes.edit', $rootCause) }}"
                    class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700"
                >
                    {{ __('Edit') }}
                </a>
                <form method="POST" action="{{ route('web.root-causes.destroy', $rootCause) }}" onsubmit="return confirm('{{ __('Are you sure you want to delete this root cause?') }}')">
                    @csrf
                    @method('DELETE')
                    <button 
                        type="submit"
                        class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700"
                    >
                        {{ __('Delete') }}
                    </button>
                </form>
            </div>
        </div>

        @if (session('success'))
            <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-md p-4">
                <p class="text-green-800 dark:text-green-200 text-sm">{{ session('success') }}</p>
            </div>
        @endif

        <!-- Data Issues Section -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-neutral-200 dark:border-neutral-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-zinc-700">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ __('Data Issues') }} ({{ $rootCause->dataIssues->count() }})
                </h2>
            </div>
            @if ($rootCause->dataIssues->isEmpty())
                <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                    {{ __('No data issues associated with this root cause.') }}
                </div>
            @else
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
                                {{ __('Business Assets') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-zinc-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($rootCause->dataIssues as $dataIssue)
                            <tr class="hover:bg-gray-50 dark:hover:bg-zinc-700">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    <a href="{{ route('web.data-issues.show', $dataIssue) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                        {{ $dataIssue->name }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $dataIssue->description ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $dataIssue->business_assets_count }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <div class="pt-4">
            <a href="{{ route('web.root-causes.index') }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                {{ __('Back to Root Causes') }}
            </a>
        </div>
    </div>
</x-layouts::app>
