@props([
    'allow-close' => true,
    'color' => 'bg-gray-200',
])
<div {{
    $attributes->class([
        "flex flex-row items-center h-full px-2 py-1 gap-1 $color rounded",
        'cursor-pointer hover:shadow-sm active:bg-gray-200' => $allowClose,
        'cursor-not-allowed' => !$allowClose,
    ])
}}>
    <div class="flex-none">
        <x-icon.tag class="w-3 h-3" />
    </div>
    <div class="flex-grow">
        <span class="text-xs text-gray-600">{{ $slot }}</span>
    </div>

    @if ($allowClose)
        <div class="flex-none">
            <x-icon.close class="w-3 h-3 text-gray-600" />
        </div>
    @endif
</div>
