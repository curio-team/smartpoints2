@props([
    'disabled' => false,
    'unimportant' => false,
    'tight' => false,
    'zebra' => false,
])
<td {{ $attributes->class([
    'border',
    'md:px-4 px-2 py-1 md:py-2' => !$tight,
    'bg-gray-100' => $zebra,
    'p-2' => $tight,
    'bg-gray-200' => $disabled,
    'border-gray-200' => !$disabled,
    'text-xs md:text-base' => $unimportant,
]) }}>
    {{ $slot }}
</td>
