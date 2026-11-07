@props([
    /** @var array<int, array{role: string, users: iterable<\App\Models\User>}> */
    'groups' => [],
    'title' => null,
])

<flux:card class="space-y-4">
    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
        {{ $title ?? __('Responsibilities') }}
    </h2>

    <dl class="space-y-3">
        @foreach ($groups as $group)
            @php
                $groupUsers = collect($group['users']);
                $isMine = $groupUsers->contains(fn ($user) => $user->id === auth()->id());
            @endphp
            <div class="flex items-start gap-4">
                <dt class="w-40 shrink-0 text-sm font-medium text-gray-500 dark:text-gray-400">
                    {{ $group['role'] }}
                </dt>
                <dd class="flex-1">
                    <x-catalog.people :users="$groupUsers" />
                    @if ($isMine)
                        <div class="mt-1">
                            <flux:badge color="green" size="sm" icon="check">
                                {{ __('You are the :role', ['role' => $group['role']]) }}
                            </flux:badge>
                        </div>
                    @endif
                </dd>
            </div>
        @endforeach
    </dl>
</flux:card>
