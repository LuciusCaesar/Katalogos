@props([
    'model' => null,
])

<p class="text-xs text-gray-400 dark:text-gray-500">
    {{ __('Created') }}: {{ $model->created_at->format('Y-m-d H:i') }}
    <span class="mx-1">·</span>
    {{ __('Last updated') }}: {{ $model->updated_at->format('Y-m-d H:i') }}
</p>
