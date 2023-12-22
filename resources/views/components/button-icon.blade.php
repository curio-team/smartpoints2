@props(['icon', 'compact' => false])
<button {{ $attributes->class([
    'flex flex-row items-center justify-center cursor-pointer gap-1 rounded hover:shadow-sm active:bg-gray-200',
    'h-full p-2 bg-emerald-300' => !$compact,
    'p-1' => $compact,
])->merge(['type' => 'button'])
}}>
    <x-dynamic-component :component="'icon.'.$icon" :compact="$compact" />
    @if ($slot->isNotEmpty())
        <span class="font-semibold @if($compact) text-sm @endif">{{ $slot }}</span>
    @endif
</button>
