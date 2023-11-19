@props([
    'loop',
    'fbmsActive',
    'currentWeek',
    'feedbackmoment'
])

@if(isset($fbmsActive))
    <th {{ $attributes->class([
        'border px-1 py-1 md:py-2',
        'bg-gray-100' => $loop->parent->even,
        'text-gray-300' => !$fbmsActive->pluck('id')->contains($feedbackmoment->id),
        'text-red-400' => (!$fbmsActive->pluck('id')->contains($feedbackmoment->id) && ($feedbackmoment->week < $currentWeek)),
    ]) }}
    
    @mouseenter="hoverColumn = '{{ $feedbackmoment->code }}'"
    @mouseleave="hoverColumn = null"
    
    >{{ $slot }}</th>
@else
    <th {{ $attributes->class([
        'border px-1 py-1 md:py-2',
        'bg-gray-100' => $loop->parent->even,
    ]) }}
    
    @mouseenter="hoverColumn = '{{ $feedbackmoment->code }}'"
    @mouseleave="hoverColumn = null"

    >{{ $slot }}</th>
@endif
