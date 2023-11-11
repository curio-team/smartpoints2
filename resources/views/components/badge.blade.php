@props([
    'color' => 'bg-sky-400',
])
<div {{
    $attributes->class([
        "grid place-content-center h-5 w-5 rounded-full $color text-xs",
    ])
}}>
    <span class="absolute animate-ping inline-flex left-0 top-0 h-4 w-4 rounded-full {{$color}} opacity-75"></span>
    {{ $slot }}
</div>
