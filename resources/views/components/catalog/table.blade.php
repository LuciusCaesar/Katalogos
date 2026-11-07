@props([
    /** @var array<int, string|array{label: string, align?: string}> */
    'columns' => [],
])

<div class="overflow-hidden rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-zinc-800">
    <div class="overflow-x-auto">
        <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-zinc-700">
                <tr>
                    @foreach ($columns as $column)
                        @php
                            $label = is_array($column) ? $column['label'] : $column;
                            $align = is_array($column) ? ($column['align'] ?? 'left') : 'left';
                            $alignClass = match ($align) {
                                'right' => 'text-right',
                                'center' => 'text-center',
                                default => 'text-left',
                            };
                        @endphp
                        <th class="px-6 py-3 {{ $alignClass }} text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            {{ $label }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-zinc-800">
                {{ $slot }}
            </tbody>
        </table>
    </div>
</div>
