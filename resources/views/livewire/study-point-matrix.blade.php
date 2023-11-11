<form class="relative h-full flex flex-col overflow-auto content-start"
    x-data="{
        editMode: {{ auth()->user()->type === 'teacher' ? 'true' : 'false' }},
        showFilters: true,
        hoverRow: null,
        hoverColumn: null,
        changesMade: {},
    }"
    x-on:study-point-matrix-changed.window="changesMade = {}"
    wire:submit="save">
    @php
    $currentWeek = \App\Models\SchoolWeek::getCurrentWeekNumber() ?? 0;
    @endphp
    <div class="flex flex-col gap-2 bg-gray-100 shadow p-2 px-4">
        <div class="flex flex-row items-center justify-between gap-2">
            <div>
                @teacher
                    <x-input.select wire:model.live="selectedBlokKey">
                        @foreach ($matrixes as $key => $otherMatrix)
                            <option value="{{ $key }}">
                                {{ $otherMatrix->blok }}
                            </option>
                        @endforeach
                    </x-input.select>
                @else
                    <h2 class="font-bold capitalize">{{ $matrix->blok }}</h2>
                @endteacher
                <span class="text-sm italic">
                    {{ \Carbon\Carbon::parse($matrix->datum_start)->format('d-m-Y') }}
                    -
                    {{ \Carbon\Carbon::parse($matrix->datum_eind)->format('d-m-Y') }}
                </span>
            </div>
            <div class="flex justify-end flex-row flex-grow gap-2 items-stretch">
                @teacher
                <div x-cloak
                    x-transition
                    class="self-end"
                    x-show="showFilters">
                    <x-input.select wire:model.live="selectedGroupId">
                        <option disabled value="-1">
                            @lang('Select a group')
                        </option>
                        @foreach ($groups as $group)
                            <option value="{{ $group->id }}"
                                @if ($group->id === $this->selectedGroupId)
                                    selected
                                @endif>
                                {{ $group->name }}
                            </option>
                        @endforeach
                    </x-input.select>
                </div>
                @endteacher
            </div>
            <div class="flex flex-row gap-2 flex-none">
                @teacher
                    <x-button-icon icon="filter"
                        @click="showFilters = !showFilters" />
                    <x-button-icon icon="edit"
                        @click="editMode = !editMode">
                        @lang('Edit')
                    </x-button-icon>
                    <x-button-icon icon="save"
                        type="submit"
                        x-cloak
                        class="relative"
                        x-show="Object.keys(changesMade).length > 0">
                        <x-badge color="bg-red-500"
                            class="absolute -top-1 -left-1">
                            <span x-text="Object.keys(changesMade).length"></span>
                        </x-badge>
                        @lang('Save')
                    </x-button-icon>
                @endteacher
            </div>
        </div>
    </div>
    <div class="overflow-auto flex-grow">
        <table class="table-auto border-collapse border border-gray-400"
            style="min-width: {{ 5 * $matrix->totalFeedbackmomenten }}em">
            <thead class="sticky top-0 bg-white shadow">
                <tr>
                    <x-table.th disabled></x-table.th>
                    <x-table.th unimportant>Leerlijn:</x-table.th>
                    @foreach ($matrix->vakken as $vak)
                        <x-table.th colspan="{{ collect($vak->modules)->pluck('feedbackmomenten')->map(fn($v) => collect($v)->toArray())->flatten()->count() }}">{{ $vak->vak }}</x-table.th>
                    @endforeach
                </tr>
                <tr>
                    <x-table.th disabled></x-table.th>
                    <x-table.th unimportant>Code:</x-table.th>
                    @foreach ($matrix->vakken as $vak)
                        @foreach ($vak->modules as $module)
                            @foreach ($module->feedbackmomenten as $feedbackmoment)
                                <x-table.th>{{ $feedbackmoment->code }}</x-table.th>
                            @endforeach
                        @endforeach
                    @endforeach
                </tr>
                <tr>
                    <x-table.th disabled></x-table.th>
                    <x-table.th unimportant>Week:</x-table.th>
                    @foreach ($matrix->vakken as $vak)
                        @foreach ($vak->modules as $module)
                            @foreach ($module->feedbackmomenten as $fbmKey => $feedbackmoment)
                                <x-table.th x-bind:class="{
                                    'outline outline-[2px] outline-pink-500 rounded border-offset-[-2px]': {{ $currentWeek }} >= {{ $feedbackmoment->week }} && {{ $currentWeek }} < {{ $module->feedbackmomenten[$fbmKey + 1]->week ?? '100' }},
                                }">
                                    {{ $feedbackmoment->week }}
                                </x-table.th>
                            @endforeach
                        @endforeach
                    @endforeach
                </tr>
                <tr>
                    <x-table.th disabled></x-table.th>
                    <x-table.th unimportant>Points:</x-table.th>
                    @foreach ($matrix->vakken as $vak)
                        @foreach ($vak->modules as $module)
                            @foreach ($module->feedbackmomenten as $fbmKey => $feedbackmoment)
                                <x-table.th>
                                    {{ $feedbackmoment->points }}
                                </x-table.th>
                            @endforeach
                        @endforeach
                    @endforeach
                </tr>
            </thead>
            <tbody>
                <tr class="border-y-2 border-gray-200">
                    <x-table.th unimportant>Student</x-table.th>
                    <x-table.th unimportant>Total</x-table.th>
                    <x-table.th colspan="{{ $matrix->totalFeedbackmomenten }}">Feedback</x-table.th>
                </tr>
                @foreach ($students as $key => $student)
                    <x-table.tr zebra="{{ $loop->even }}"
                        wire:key="student-{{ $student->id }}">
                        <x-table.td>
                            {{ $student->name }}
                            <small>({{ $student->id }})</small>
                        </x-table.td>
                        <x-table.td>
                            {{ $student->total }}
                        </x-table.td>
                        @foreach ($matrix->vakken as $vak)
                            @foreach ($vak->modules as $module)
                                @foreach ($module->feedbackmomenten as $fbmKey => $feedbackmoment)
                                    <x-table.td tight class="p-1"
                                        x-bind:class="{
                                            'bg-emerald-200': hoverRow === '{{ $student->id }}' || hoverColumn === '{{ $feedbackmoment->code }}',
                                            'bg-emerald-400': hoverRow === '{{ $student->id }}' && hoverColumn === '{{ $feedbackmoment->code }}',
                                            'border-x-[2px] border-pink-500 rounded border-offset-[-2px]': {{ $currentWeek }} >= {{ $feedbackmoment->week }} && {{ $currentWeek }} < {{ $module->feedbackmomenten[$fbmKey + 1]->week ?? '100' }},
                                        }"
                                        @mouseenter="hoverRow = '{{ $student->id }}'; hoverColumn = '{{ $feedbackmoment->code }}'"
                                        @mouseleave="hoverRow = null; hoverColumn = null">
                                        <x-input.text type="number" class="w-full h-full text-center" step="1"
                                            wire:model="students.{{ $key }}.feedbackmomenten.{{ $module->version_id }}-{{ $feedbackmoment->id }}"
                                            min="0" max="{{ $feedbackmoment->points }}"
                                            x-on:input="changesMade['{{ $module->version_id }}-{{ $feedbackmoment->id }}'] = true"
                                            x-bind:disabled="!editMode" />
                                    </x-table.td>
                                @endforeach
                            @endforeach
                        @endforeach
                    </x-table.tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div id="loadingIndicator" wire:ignore>
        <div class="absolute inset-0 grid place-content-center bg-gray-100 bg-opacity-75">
            <x-icon.loading class="w-10 h-10 text-gray-600 animate-spin" />
        </div>
    </div>

    {{-- Debug by outputting the matrix and students to console --}}
    <script>
        console.log(@json($matrix));
        console.log(@json($students));

        document.addEventListener('livewire:initialized', () => {
            const loading = document.getElementById('loadingIndicator');
            loading.setAttribute('wire:loading', '');
        });
    </script>
</form>
