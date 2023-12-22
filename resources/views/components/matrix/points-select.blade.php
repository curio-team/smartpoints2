@props(['type' => 'a'])

<div>
    <input type="radio" x-on:change="showPoints = $el.value" x-bind:checked="showPoints == $el.value" value="a" id="pointsChangerA-{{ $type }}"><label for="pointsChangerA-{{ $type }}">A</label>
    <input type="radio" x-on:change="showPoints = $el.value" x-bind:checked="showPoints == $el.value" value="b" id="pointsChangerB-{{ $type }}"><label for="pointsChangerB-{{ $type }}">B</label>
</div>
