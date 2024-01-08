<table class="table-auto border-collapse border border-gray-400">
    <tbody>
        @foreach ($students as $key => $student)
            <?php $studentIndex = str_pad($loop->iteration, 3, "0", STR_PAD_LEFT); ?>
            <?php $zebra = $loop->even; ?>
            <x-table.tr zebra="{{ $loop->even }}" wire:key="student-{{ $student->id }}">
                <?php
                    $color = $loop->even ? 'bg-gray-100' : 'bg-white';
                    if($currentWeek >= 12)
                    {
                        $percentage = round($student->totalBpoints / $this->blok->totalBpoints * 100);
                        if($percentage >= 98) $color = 'bg-green-400';
                        elseif($percentage >= 80) $color = 'bg-orange-300';
                        else $color = 'bg-red-400';
                    }
                ?>

                <x-table.td
                    style="width: 300px;"
                    class="whitespace-nowrap left-0 sticky z-10 flex justify-between items-center @if($currentWeek > 12) {{ $color }} @endif"
                    @mouseenter="hoverRow = '{{ $student->id }}'"
                    @mouseleave="hoverRow = null">
                    <a class="truncate" target="_blank" href="{{ route('student.show', $student->id) }}">{{ $student->name }}</a>
                    <span> {{$student->totalBpoints}} / {{$this->blok->totalBpoints}} </span>
                </x-table.td>

                @foreach ($this->blok->vakken as $vak)
                    <td class="p-0 relative z-0 h-auto" style="min-width: 80px;"
                        wire:key="vak-{{ $vak->uitvoer_id }}-student-{{ $student->id }}-blok-{{ $this->blok->id }}-{{ time() }}"
                        @mouseenter="hoverRow = '{{ $student->id }}'; hoverColumn = '{{ $vak->vak }}'"
                        @mouseleave="hoverRow = null; hoverColumn = null">


                        <input type="number" class="w-full h-full nospin text-center absolute bottom-0 top-0 left-0 right-0 border
                        @if($zebra) bg-gray-100 @else bg-white @endif"
                        x-bind:class="{

                            '!bg-emerald-200': hoverRow === '{{ $student->id }}' || hoverColumn === '{{ $vak->uitvoer_id }}',
                            '!bg-emerald-400': hoverRow === '{{ $student->id }}' && hoverColumn === '{{ $vak->uitvoer_id }}',
                            'text-red-400 font-semibold' : $el.value < 2 && $el.value.length == 1,
                            'border-yellow-400' : !$el.value.length && {{ $currentWeek }} >= 12,
                        }"
                        step="1" min="0" max="2"
                        tabindex="{{ $loop->iteration.$studentIndex }}"
                        wire:model="students.{{ $key }}.bPointsOverview.{{$vak->uitvoer_id}}"
                        x-on:input="changesMade['{{ $student->id }} - b{{ $vak->uitvoer_id }}'] = true" --}}
                        x-on:focus="hoverRow = '{{ $student->id }}'; hoverColumn = '{{ $vak->uitvoer_id }}'" />

                    </td>
                @endforeach
            </x-table.tr>
        @endforeach
    </tbody>
</table>
