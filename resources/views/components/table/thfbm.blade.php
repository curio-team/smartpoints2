@props([
    'loop',
    'fbmsActive',
    'currentWeek',
    'feedbackmoment',
    'studentView' => false
])

@if(isset($fbmsActive))
    <th {{ $attributes->class([
        'border px-1 py-1 md:py-2',
        'bg-gray-100' => $loop->parent->even,
        'text-gray-400' => !$fbmsActive->pluck('id')->contains($feedbackmoment->id),
        'text-red-400' => (!$fbmsActive->pluck('id')->contains($feedbackmoment->id) && ($feedbackmoment->week < $currentWeek) && !$studentView),
        'cursor-pointer' => $attributes->has('wire:click'),
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
