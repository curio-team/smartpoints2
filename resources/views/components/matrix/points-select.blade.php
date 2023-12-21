<x-input.select id="pointsChanger" x-model="showPoints" class="h-9"
    x-on:change="showPoints = $el.value">
    <option value="a">A</option>
    <option value="b">B</option>
    <option value="c">C</option>
</x-input.select>
