@props([
    /** @var array<int, array{label: string, href: string|null}> */
    'items' => [],
])

<flux:breadcrumbs>
    @foreach ($items as $item)
        @if ($loop->last || empty($item['href']))
            <flux:breadcrumbs.item>{{ $item['label'] }}</flux:breadcrumbs.item>
        @else
            <flux:breadcrumbs.item :href="$item['href']">{{ $item['label'] }}</flux:breadcrumbs.item>
        @endif
    @endforeach
</flux:breadcrumbs>
