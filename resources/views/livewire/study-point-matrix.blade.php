<div x-data="{
        hoverRow: null,
        hoverColumn: null,
        changesMade: {},
    }"
    x-on:study-point-matrix-changed.window="changesMade = {}">
    <?php $currentWeek = \App\Models\SchoolWeek::getCurrentWeekNumber() ?? 0; ?>

    <div class="flex flex-row items-center justify-between bg-gray-100 shadow p-2 px-4 sticky top-0 z-50 h-14">
        <div class="flex flex-row items-center gap-3">
            <x-input.select wire:model.live="selectedGroupId" id="groupChanger" class="h-9"
                x-on:change="history.pushState({groupId: document.getElementById('groupChanger').value}, '', '/groups/' + document.getElementById('groupChanger').value)">
                <option disabled value="-1">Selecteer een klas</option>
                @foreach ($groups as $group)
                    <option value="{{ $group['group_id'] }}">{{ $group['name'] }}</option>
                @endforeach
            </x-input.select>
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
            <span class="text-gray-300 font-bold">grijs:</span> dit fbm is in de toekomst
            | <span class="text-red-400 font-bold">rood:</span> dit fbm is nog niet ingevuld maar de week is voorbij
            | totaal bij studenten is een optelling van alle <span class="font-bold">zwarte</span> fbm's
        </span>
    </div>

    @if(isset($this->blok))
        <div class="sticky top-[56px] z-50">
            <div class="overflow-auto syncscroll" name="syncTable">
                <table class="table-auto border-collapse border border-gray-400">
                    <thead class="bg-white shadow">
                        <tr>
                            <x-table.th disabled class="sticky left-0" style="min-width: 300px;"></x-table.th>
                            @foreach ($blok->vakken as $vak)
                                <x-table.th zebra="{{ $loop->even }}" colspan="{{ count($vak->feedbackmomenten) }}">{{ $vak->vak }}</x-table.th>
                            @endforeach
                        </tr>
                        <tr>
                            <x-table.th disabled class="sticky left-0"></x-table.th>
                            @foreach ($blok->vakken as $vak)
                                @foreach ($vak->feedbackmomenten as $feedbackmoment)
                                    <x-table.thfbm style="min-width: 50px;" :loop="$loop" :fbmsActive="$fbmsActive" :currentWeek="$currentWeek" :feedbackmoment="$feedbackmoment" class="text-sm">{{ $feedbackmoment->code }}</x-table.thfbm>
                                @endforeach
                            @endforeach
                        </tr>
                        <tr>
                            <x-table.th disabled class="sticky left-0 italic text-sm text-right font-normal pe-2">week:</x-table.th>
                            @foreach ($blok->vakken as $vak)
                                @foreach ($vak->feedbackmomenten as $feedbackmoment)
                                    <x-table.thfbm :loop="$loop" :fbmsActive="$fbmsActive" :currentWeek="$currentWeek" :feedbackmoment="$feedbackmoment">{{ $feedbackmoment->week }}</x-table.thfbm>
                                @endforeach
                            @endforeach
                        </tr>
                        <tr>
                            <x-table.th disabled class="sticky left-0 italic text-sm text-right font-normal pe-2">punten:</x-table.th>
                            @foreach ($blok->vakken as $vak)
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
                        <?php $studentIndex = str_pad($loop->index, 3, "0", STR_PAD_LEFT); ?>
                        <?php $zebra = $loop->even; ?>
                        <x-table.tr zebra="{{ $loop->even }}"
                            wire:key="student-{{ $student->id }}">

                            <?php
                            $color = "";
                            if($student->totalPointsToGainUntilNow > 0)
                            {
                                $percentage = $student->totalPoints / $student->totalPointsToGainUntilNow * 100;
                                if($percentage >= 98) $color = 'bg-green-400';
                                elseif($percentage >= 80) $color = 'bg-orange-400';
                                else $color = 'bg-red-400';
                            }
                            ?>

                            <x-table.td style="width: 300px;" class="whitespace-nowrap left-0 sticky z-10 flex justify-between items-center {{ $color }}" zebra="{{ $loop->even }}">
                                <span class="truncate">{{ $student->name }}</span>
                                <span>{{ $student->totalPoints }} / {{ $student->totalPointsToGainUntilNow }}</span>
                            </x-table.td>
                            <?php $columnIndex = 0; ?>
                            @foreach ($blok->vakken as $vak)
                                @foreach ($vak->feedbackmomenten as $feedbackmoment)
                                    <?php $columnIndex++; ?>
                                    <td class="p-0 relative z-0" style="min-width: 50px;"
                                        @mouseenter="hoverRow = '{{ $student->id }}'; hoverColumn = '{{ $feedbackmoment->code }}'"
                                        @mouseleave="hoverRow = null; hoverColumn = null">
                                        <input type="number" class="nospin text-center absolute bottom-0 top-0 left-0 right-0 border
                                            @if($zebra) bg-gray-100 @else bg-white @endif"
                                            x-bind:class="{
                                                '!bg-emerald-200': hoverRow === '{{ $student->id }}' || hoverColumn === '{{ $feedbackmoment->code }}',
                                                '!bg-emerald-400': hoverRow === '{{ $student->id }}' && hoverColumn === '{{ $feedbackmoment->code }}',
                                                '!text-gray-300' : {{ $feedbackmoment->week }} > {{ $currentWeek }},
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
        <div class="absolute inset-0 grid place-content-center bg-gray-100 bg-opacity-75">
            <x-icon.loading class="w-10 h-10 text-gray-600 animate-spin" />
        </div>
    </div>

    <script>
        document.addEventListener('livewire:initialized', () => {
            const loading = document.getElementById('loadingIndicator');
            loading.setAttribute('wire:loading', '');

            window.addEventListener("popstate", (event) => {
                if(event.state)
                {
                    @this.dispatch('new-group-id-from-state', {id: event.state.groupId}); 
                }
                else
                {
                    window.location = document.location;
                }
            });
        });
    </script>
    <script src="/js/syncscroll.js" />
</div>