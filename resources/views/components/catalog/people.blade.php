@props([
    /** @var iterable<\App\Models\User> */
    'users' => [],
    'empty' => '-',
])

@php
    $people = collect($users);
@endphp

@if ($people->isEmpty())
    <span class="text-gray-400 dark:text-gray-500">{{ $empty }}</span>
@else
    <div class="flex flex-wrap items-center gap-x-3 gap-y-1">
        @foreach ($people as $user)
            <span class="inline-flex items-center gap-1.5">
                <flux:avatar :name="$user->name" :initials="$user->initials()" size="xs" circle />
                <span class="text-sm text-gray-900 dark:text-gray-100">{{ $user->name }}</span>
            </span>
        @endforeach
    </div>
@endif
