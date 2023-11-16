@props([
    'loop',
    'fbmsActive',
    'currentWeek',
    'feedbackmoment'
])

@if(isset($fbmsActive))
    <th {{ $attributes->class([
        'border px-1 py-1 md:py-2',
        'bg-gray-100' => $loop->parent->parent->even,
        'text-gray-300' => !$fbmsActive->pluck('id')->contains($feedbackmoment->id),
        'text-red-400' => (!$fbmsActive->pluck('id')->contains($feedbackmoment->id) && ($feedbackmoment->week < $currentWeek)),
    ]) }}>{{ $slot }}</th>
@else
    <th {{ $attributes->class([
        'border px-1 py-1 md:py-2',
        'bg-gray-100' => $loop->parent->parent->even,
    ]) }}>{{ $slot }}</th>
@endif
