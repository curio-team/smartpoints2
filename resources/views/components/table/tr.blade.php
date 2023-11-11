@props([
    'zebra' => false,
])
<tr {{ $attributes->class([
    'bg-gray-100' => $zebra,
]) }}>
    {{ $slot }}
</tr>
