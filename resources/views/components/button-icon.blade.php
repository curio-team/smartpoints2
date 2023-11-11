<button {{ $attributes->class([
    'flex flex-row items-center justify-center h-full p-2 cursor-pointer gap-1 bg-emerald-300 rounded hover:shadow-sm active:bg-gray-200'
]) }}>
    <x-dynamic-component :component="'icon.'.$icon" class="w-6 h-6" />
    @if ($slot->isNotEmpty())
        <span class="font-semibold">{{ $slot }}</span>
    @endif
</button>
