<div x-data="{
    hoverRow: null,
    hoverColumn: null,
    changesMade: {},
}"
x-on:study-point-matrix-changed.window="changesMade = {}">
    @php $currentWeek = \App\Models\SchoolWeek::getCurrentWeekNumber() ?? 0; @endphp

    <div class="flex flex-row items-center justify-between bg-gray-100 shadow p-2 px-4 sticky top-0 z-50 h-14">
        <div class="flex flex-row items-center gap-3">
            <div>
                <x-input.select wire:model.live="selectedGroupId" id="groupChanger" class="h-9">
                    <option disabled value="-1">Selecteer een klas</option>
                    @foreach ($groups as $group)
                        <option value="{{ $group['group_id'] }}">
                            {{ $group['name'] }}
                        </option>
                    @endforeach
                </x-input.select>
            </div>
            <div x-show="$wire.selectedBlokId != -1">
                <x-input.select wire:model.live="selectedBlokId" id="blokChanger" class="h-9">
                    <option value="-1">Huidig blok</option>
                    @foreach ($blokken as $blok)
                        <option value="{{ $blok->id }}" :selected="$wire.selectedBlokId == {{ $blok->id }}">
                            {{ $blok->blok }}
                        </option>
                    @endforeach
                </x-input.select>
            </div>
            <x-button-icon icon="save"
                x-cloak
                wire:click="save"
                class="relative h-9"
                x-show="Object.keys(changesMade).length > 0">
                <x-badge color="bg-red-500"
                    class="absolute -top-1 -right-1">
                    <span x-text="Object.keys(changesMade).length"></span>
                </x-badge>
                @lang('Save')
            </x-button-icon>
        </div>
        <span class="ps-2 text-sm italic">
            <span class="text-gray-300 font-bold">grijs:</span> fbm is in de toekomst
            | <span class="text-red-400 font-bold">rood:</span> voor hele klas nog niet ingevuld
            | totaal is een optelling van alle <span class="font-bold">zwarte</span> fbm's
        </span>
    </div>

    @if(isset($this->blok))
        <div class="sticky top-[56px] z-50">
            <div class="overflow-auto syncscroll" name="syncTable">
                <table class="table-auto border-collapse border border-gray-400">
                    <thead class="bg-white shadow">
                        <tr>
                            <x-table.th disabled class="sticky left-0" style="min-width: 300px;"></x-table.th>
                            @foreach ($this->blok->vakken as $vak)
                                <x-table.th zebra="{{ $loop->even }}" colspan="{{ count($vak->feedbackmomenten) }}">{{ $vak->vak }}</x-table.th>
                            @endforeach
                        </tr>
                        <tr>
                            <x-table.th disabled class="sticky left-0"></x-table.th>
                            @foreach ($this->blok->vakken as $vak)
                                @foreach ($vak->feedbackmomenten as $feedbackmoment)
                                    <x-table.thfbm style="min-width: 50px;" :loop="$loop" :fbmsActive="$fbmsActive" :currentWeek="$currentWeek" :feedbackmoment="$feedbackmoment" class="text-sm">{{ $feedbackmoment->code }}</x-table.thfbm>
                                @endforeach
                            @endforeach
                        </tr>
                        <tr>
                            <x-table.th disabled class="sticky left-0 italic text-sm text-right font-normal pe-2">week:</x-table.th>
                            @foreach ($this->blok->vakken as $vak)
                                @foreach ($vak->feedbackmomenten as $feedbackmoment)
                                    <x-table.thfbm :loop="$loop" :fbmsActive="$fbmsActive" :currentWeek="$currentWeek" :feedbackmoment="$feedbackmoment">{{ $feedbackmoment->week }}</x-table.thfbm>
                                @endforeach
                            @endforeach
                        </tr>
                        <tr>
                            <x-table.th disabled class="sticky left-0 italic text-sm text-right font-normal pe-2">punten:</x-table.th>
                            @foreach ($this->blok->vakken as $vak)
                                @foreach ($vak->feedbackmomenten as $fbmKey => $feedbackmoment)
                                    <x-table.thfbm :loop="$loop" :fbmsActive="$fbmsActive" :currentWeek="$currentWeek" :feedbackmoment="$feedbackmoment">{{ $feedbackmoment->points }}</x-table.thfbm>
                                @endforeach
                            @endforeach
                        </tr>
                    </thead>
                </table>
            </div>
        </div>

        <form class="overflow-auto z-0 syncscroll" name="syncTable" wire:submit="save">
            {{-- This button is to make saving by enter key work: --}}
            <input type="submit" style="display: none;">

            <table class="table-auto border-collapse border border-gray-400">
                <tbody>
                    @foreach ($students as $key => $student)
                        @php $studentIndex = str_pad($loop->index, 3, "0", STR_PAD_LEFT); @endphp
                        @php $zebra = $loop->even; @endphp
                        <x-table.tr zebra="{{ $loop->even }}"
                            wire:key="student-{{ $student->id }}-blok-{{ $blok->id }}">

                            @php
                            $color = $loop->even ? 'bg-gray-100' : 'bg-white';
                            if($student->totalPointsToGainUntilNow > 0)
                            {
                                $percentage = round($student->totalPoints / $student->totalPointsToGainUntilNow * 100);
                                if($percentage >= 98) $color = 'bg-green-400';
                                elseif($percentage >= 80) $color = 'bg-orange-300';
                                else $color = 'bg-red-400';
                            }
                            @endphp

                            <x-table.td
                                style="width: 300px;"
                                class="whitespace-nowrap left-0 sticky z-10 flex justify-between items-center {{ $color }}"
                                @mouseenter="hoverRow = '{{ $student->id }}'"
                                @mouseleave="hoverRow = null">
                                <a class="truncate" target="_blank" href="{{ route('student.show', $student->id) }}">{{ $student->name }}</a>
                                <span>{{ $student->totalPoints }} / {{ $student->totalPointsToGainUntilNow }}</span>
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
        </form>
    @endif

    <div id="loadingIndicator" wire:ignore>
        <div class="fixed z-[100] inset-0 grid place-content-center bg-gray-100 bg-opacity-75">
            <x-icon.loading class="w-10 h-10 text-gray-600 animate-spin" />
        </div>
    </div>

    <script>
        document.addEventListener('livewire:initialized', () => {
            const loading = document.getElementById('loadingIndicator');
            loading.setAttribute('wire:loading', '');

            Livewire.hook('morph.updated', ({ el, component }) => {
                syncscroll.reset();
            })
        });
    </script>
    <script src="/js/syncscroll.js"></script>
</div>
