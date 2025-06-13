<table class="table-auto border-collapse border border-gray-400">
    <tbody>
        @foreach ($students as $key => $student)
            @php $studentIndex = str_pad($loop->index, 3, "0", STR_PAD_LEFT); @endphp
            @php $zebra = $loop->even; @endphp
            <x-table.tr zebra="{{ $loop->even }}"
                wire:key="student-{{ $student->id }}-blok-{{ $blok->id }}">

                @php
                $color = $loop->even ? 'bg-gray-100' : 'bg-white';

                if($student->totalAverage >= 5.5) {
                    $color = $loop->even ? 'bg-green-100' : 'bg-green-200';
                } else {
                    $color = $loop->even ? 'bg-red-100' : 'bg-red-200';
                }
                @endphp

                <x-table.td
                    style="width: 300px;"
                    class="whitespace-nowrap left-0 sticky z-10 flex justify-between items-center {{ $color }}"
                    @mouseenter="hoverRow = '{{ $student->id }}'"
                    @mouseleave="hoverRow = null">
                    <a class="truncate" target="_blank" href="{{ route('student.show', $student->id) }}">{{ $student->name }}</a>
                    <span>{{ locale_number_format($student->totalAverage, 1) }}</span>
                </x-table.td>
                @php $columnIndex = 0; @endphp
                @foreach ($this->blok->vakken as $vak)
                    @foreach ($vak->feedbackmomenten as $feedbackmoment)
                        @php $columnIndex++; @endphp
                        <td class="p-0 relative z-0 h-auto" style="min-width: 50px;"
                            wire:key="fbm-{{ $feedbackmoment->id }}-student-{{ $student->id }}-blok-{{ $blok->id }}-{{ time() }}"
                            @mouseenter="hoverRow = '{{ $student->id }}'; hoverColumn = '{{ $feedbackmoment->code }}'"
                            @mouseleave="hoverRow = null; hoverColumn = null">
                            <input type="number" class="w-full h-full nospin text-center absolute bottom-0 top-0 left-0 right-0 border
                                @if($zebra) bg-gray-100 @else bg-white @endif"
                                x-bind:class="{
                                    '!bg-emerald-200': hoverRow === '{{ $student->id }}' || hoverColumn === '{{ $feedbackmoment->code }}',
                                    '!bg-emerald-400': hoverRow === '{{ $student->id }}' && hoverColumn === '{{ $feedbackmoment->code }}',
                                    '!text-gray-400' : {{ $feedbackmoment->week }} > {{ $currentWeek }},
                                    'text-red-400 font-semibold' : $el.value < {{ $feedbackmoment->points }} && $el.value.length == 1,
                                    'border-yellow-400' : !$el.value.length && {{ $feedbackmoment->week }} < {{ $currentWeek }},
                                }"
                                step="1" min="0" max="{{ $feedbackmoment->points }}"
                                tabindex="{{ $columnIndex.$studentIndex }}"
                                wire:model="students.{{ $key }}.feedbackmomenten.{{ $feedbackmoment->id }}"
                                x-on:input="changesMade['{{ $student->id }} - {{ $feedbackmoment->id }}'] = true"
                                x-on:focus="hoverRow = '{{ $student->id }}'; hoverColumn = '{{ $feedbackmoment->code }}'" />
                        </td>
                    @endforeach
                @endforeach
            </x-table.tr>
        @endforeach
    </tbody>
</table>
