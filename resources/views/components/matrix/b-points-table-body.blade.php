<table class="table-auto border-collapse border border-gray-400">
    <tbody>

        @foreach ($students as $key => $student)
            <?php $studentIndex = str_pad($loop->index, 3, "0", STR_PAD_LEFT); ?>
            <?php $zebra = $loop->even; ?>
            <x-table.tr zebra="{{ $loop->even }}"
                wire:key="student-{{ $student->id }}">

                <?php
                $color = $loop->even ? 'bg-gray-100' : 'bg-white';
                if($student->totalPointsToGainUntilNow > 0)
                {
                    $percentage = round($student->totalPoints / $student->totalPointsToGainUntilNow * 100);
                    if($percentage >= 98) $color = 'bg-green-400';
                    elseif($percentage >= 80) $color = 'bg-orange-300';
                    else $color = 'bg-red-400';
                }
                ?>

                <x-table.td
                    style="width: 300px;"
                    class="whitespace-nowrap left-0 sticky z-10 flex justify-between items-center {{ $color }}"
                    @mouseenter="hoverRow = '{{ $student->id }}'"
                    @mouseleave="hoverRow = null">
                    <a class="truncate" target="_blank" href="{{ route('student.show', $student->id) }}">{{ $student->name }}</a>
                    <span> {{$student->totalBpoints}} / {{$blok->totalBpoints}} </span>
                </x-table.td>
                <?php $columnIndex = 0; ?>
                @foreach ($blok->vakken as $vak)
                        <td class="p-0 relative z-0 h-auto" style="min-width: 80px; text-align: center">
                            <input type="number" class="w-full h-full nospin text-center absolute bottom-0 top-0 left-0 right-0 border
                            @if($zebra) bg-gray-100 @else bg-white @endif"

                            tabindex="{{ $columnIndex.$studentIndex }}" />
                        </td>
                @endforeach
            </x-table.tr>
        @endforeach
    </tbody>
</table>
