@extends('layouts.app')

@section('main')
    <div class="overflow-auto">
        <table class="table-auto w-full border-collapse border border-gray-400"
            style="min-width: {{ 5 * $matrix->totalFeedbackmomenten }}em">
            <thead>
                <tr>
                    <th class="border bg-gray-200 md:px-4 px-2 py-1 md:py-2 text-xs md:text-base"></th>
                    <th class="border border-gray-400 md:px-4 px-2 py-1 md:py-2 text-xs md:text-base">Leerlijn:</th>
                    @foreach ($matrix->vakken as $vak)
                        <th class="border border-gray-400 md:px-4 px-2 py-1 md:py-2" colspan="{{ collect($vak->modules)->pluck('feedbackmomenten')->map(fn($v) => collect($v)->toArray())->flatten()->count() }}">{{ $vak->vak }}</th>
                    @endforeach
                </tr>
                <tr>
                    <th class="border bg-gray-200 md:px-4 px-2 py-1 md:py-2 text-xs md:text-base"></th>
                    <th class="border border-gray-400 md:px-4 px-2 py-1 md:py-2 text-xs md:text-base">Code:</th>
                    @foreach ($matrix->vakken as $vak)
                        @foreach ($vak->modules as $module)
                            @foreach ($module->feedbackmomenten as $feedbackmoment)
                                <th class="border border-gray-400 md:px-4 px-2 py-1 md:py-2">{{ $feedbackmoment->code }}</th>
                            @endforeach
                        @endforeach
                    @endforeach
                </tr>
                <tr>
                    <th class="border bg-gray-200 md:px-4 px-2 py-1 md:py-2 text-xs md:text-base"></th>
                    <th class="border border-gray-400 md:px-4 px-2 py-1 md:py-2 text-xs md:text-base">Week:</th>
                    @foreach ($matrix->vakken as $vak)
                        @foreach ($vak->modules as $module)
                            @foreach ($module->feedbackmomenten as $feedbackmoment)
                                <th class="border border-gray-400 md:px-4 px-2 py-1 md:py-2">{{ $feedbackmoment->week }}</th>
                            @endforeach
                        @endforeach
                    @endforeach
                </tr>
                <tr class="border-b-4 border-gray-200">
                    <th class="border bg-gray-200 md:px-4 px-2 py-1 md:py-2 text-xs md:text-base"></th>
                    <th class="border border-gray-400 md:px-4 px-2 py-1 md:py-2 text-xs md:text-base">Points:</th>
                    @foreach ($matrix->vakken as $vak)
                        @foreach ($vak->modules as $module)
                            @foreach ($module->feedbackmomenten as $feedbackmoment)
                                <th class="border border-gray-400 md:px-4 px-2 py-1 md:py-2">{{ $feedbackmoment->points }}</th>
                            @endforeach
                        @endforeach
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($students as $student)
                    <tr>
                        <td class="border border-gray-400 md:px-4 px-2 py-1 md:py-2">{{ $student->name }}</td>
                        <td class="border border-gray-400 md:px-4 px-2 py-1 md:py-2">{{ $student->group }}</td>
                        @foreach ($matrix->vakken as $vak)
                            @foreach ($vak->modules as $module)
                                @foreach ($module->feedbackmomenten as $feedbackmoment)
                                    <td class="border border-gray-400 md:px-4 px-2 py-1 md:py-2">
                                        @isset($student->feedbackmomenten[$feedbackmoment->code])
                                            {{ $student->feedbackmomenten[$feedbackmoment->code] }}
                                        @else
                                            -
                                        @endisset
                                    </td>
                                @endforeach
                            @endforeach
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
