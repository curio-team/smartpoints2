@props(['compact' => false])
<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" {{
    $attributes->class([
        'h-3 w-3' => $compact,
        'h-6 w-6' => !$compact,
    ]);
}}>
    {{ $slot }}
</svg>
