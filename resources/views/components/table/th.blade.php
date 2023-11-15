@props([
    'disabled' => false,
    'unimportant' => false,
    'zebra' => false,
])
<th {{ $attributes->class([
    'border px-1 py-1 md:py-2',
    'bg-gray-100' => $zebra,
    'bg-gray-200' => $disabled,
    'border-gray-200' => !$disabled,
    'text-xs md:text-sm' => $unimportant,
]) }}>
    {{ $slot }}
</th>
