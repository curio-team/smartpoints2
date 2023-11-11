<table class="table-auto w-full border-collapse border border-gray-400"
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
        @foreach ($students as $student)
            <x-table.tr zebra="{{ $loop->even }}">
                <x-table.td>{{ $student->name }}</x-table.td>
                <x-table.td>{{ $student->group }}</x-table.td>
                @foreach ($matrix->vakken as $vak)
                    @foreach ($vak->modules as $module)
                        @foreach ($module->feedbackmomenten as $feedbackmoment)
                            <x-table.td>
                                @isset($student->feedbackmomenten[$feedbackmoment->code])
                                    {{ $student->feedbackmomenten[$feedbackmoment->code] }}
                                @else
                                    -
                                @endisset
                            </x-table.td>
                        @endforeach
                    @endforeach
                @endforeach
            </x-table.tr>
        @endforeach
    </tbody>
</table>
