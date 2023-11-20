@props([
    'disabled' => false,
    'unimportant' => false,
    'zebra' => false,
    'red' => false,
])
<th {{ $attributes->class([
    'border px-1 py-1 md:py-2',
    'bg-gray-100' => $zebra,
    'bg-gray-200' => $disabled,
    'border-gray-200' => !$disabled,
    'text-xs md:text-sm' => $unimportant,
    'text-red-500 font-semibold border-yellow-400 border-2' => $red,
]) }}>
    {{ $slot }}
</th>
