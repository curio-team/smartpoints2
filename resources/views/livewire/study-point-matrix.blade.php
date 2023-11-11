<div class="relative h-full grid grid-auto-rows grid-cols-1 overflow-auto"
    x-data="{
        editMode: {{ auth()->user()->type === 'teacher' ? 'true' : 'false' }},
        hoverRow: null,
        hoverColumn: null,
    }">
    <div>
        <div class="flex flex-row items-center justify-between bg-gray-100 shadow p-2">
            <div class="flex flex-row flex-grow gap-2 items-stretch">
                {{-- Mockup --}}
                @php
                $tags = [
                    [
                        'name' => 'Vak',
                        'color' => 'bg-blue-200',
                    ],
                    [
                        'name' => 'Module',
                        'color' => 'bg-green-200',
                    ],
                ];
                @endphp
                @foreach ($tags as $tag)
                    <x-tag :color="$tag['color']" :allow-close="auth()->user()->type === 'teacher'">
                        {{ $tag['name'] }}
                    </x-tag>
                @endforeach
            </div>
            <div class="flex flex-row gap-2 flex-none">
                @if(auth()->user()->type === 'teacher')
                    <x-button-icon icon="filter" />
                    <x-button-icon icon="edit"
                        @click="editMode = !editMode">
                        @lang('Edit')
                    </x-button-icon>
                @endif
            </div>
        </div>
    </div>
    <div class="overflow-auto">
        <table class="table-auto border-collapse border border-gray-400"
            style="min-width: {{ 5 * $matrix->totalFeedbackmomenten }}em">
            <thead>
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
                            @foreach ($module->feedbackmomenten as $feedbackmoment)
                                <x-table.th>{{ $feedbackmoment->week }}</x-table.th>
                            @endforeach
                        @endforeach
                    @endforeach
                </tr>
                <tr>
                    <x-table.th disabled></x-table.th>
                    <x-table.th unimportant>Points:</x-table.th>
                    @foreach ($matrix->vakken as $vak)
                        @foreach ($vak->modules as $module)
                            @foreach ($module->feedbackmomenten as $feedbackmoment)
                                <x-table.th>{{ $feedbackmoment->points }}</x-table.th>
                            @endforeach
                        @endforeach
                    @endforeach
                </tr>
                <tr class="border-y-4 border-gray-200">
                    <x-table.th unimportant>Student</x-table.th>
                    <x-table.th unimportant>Group</x-table.th>
                    <x-table.th colspan="{{ $matrix->totalFeedbackmomenten }}">Feedback</x-table.th>
                </tr>
            </thead>
            <tbody>
                @foreach ($students as $key => $student)
                    <x-table.tr zebra="{{ $loop->even }}"
                        wire:key="student-{{ $student->id }}">
                        <x-table.td>
                            {{ $student->name }}
                            <small>({{ $student->id }})</small>
                        </x-table.td>
                        <x-table.td>{{ $student->group }}</x-table.td>
                        @foreach ($matrix->vakken as $vak)
                            @foreach ($vak->modules as $module)
                                @foreach ($module->feedbackmomenten as $feedbackmoment)
                                    <x-table.td tight class="p-1"
                                        x-bind:class="{
                                            'bg-emerald-200': hoverRow === '{{ $student->id }}' || hoverColumn === '{{ $feedbackmoment->code }}',
                                            'bg-emerald-400': hoverRow === '{{ $student->id }}' && hoverColumn === '{{ $feedbackmoment->code }}',
                                        }"
                                        @mouseenter="hoverRow = '{{ $student->id }}'; hoverColumn = '{{ $feedbackmoment->code }}'"
                                        @mouseleave="hoverRow = null; hoverColumn = null">
                                        <x-input.text type="number" class="w-full h-full text-center" step="1"
                                            wire:model.live="students.{{ $key }}.feedbackmomenten.{{ $module->version_id }}-{{ $feedbackmoment->id }}"
                                            min="0" max="{{ $feedbackmoment->points }}"
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

    {{-- Debug by outputting the matrix and students to console --}}
    <script>
        console.log(@json($matrix));
        console.log(@json($students));
    </script>
</div>
