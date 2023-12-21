<x-layouts.app class="text-base" :title="$student->name">

    <?php $currentWeek = \App\Models\SchoolWeek::getCurrentWeekNumber() ?? 0; ?>
    <div class="flex flex-col sm:flex-row items-center justify:center sm:justify-between bg-gray-100 shadow p-2 px-4 sticky top-0 z-50 sm:h-14 ">
        <div class="flex gap-2">
            <div class="flex flex-row items-center gap-3 font-bold text-xs sm:text-xl">{{ $student->name }}</div>

            <?php
            $color = 'bg-gray-200';
            if($student->totalPointsToGainUntilNow > 0)
            {
                $percentage = round($student->totalPoints / $student->totalPointsToGainUntilNow * 100);
                if($percentage >= 98) $color = 'bg-green-400';
                elseif($percentage >= 80) $color = 'bg-orange-300';
                else $color = 'bg-red-400';
            }
            ?>
            <div class="flex flex-row items-center text-xs sm:text-lg px-2 py-1 sm:h-full sm:px-2 sm:py-1 gap-1 rounded  {{ $color }}">
                {{ $student->totalPoints }} / {{ $student->totalPointsToGainUntilNow }}
            </div>
        </div>
        <span class="ps-2 text-sm italic text-xs sm:text-sm text-clip overflow-hidden hidden sm:block">
            <span class="text-gray-300 font-bold">grijs:</span> fbm is in de toekomst
            | totaal is een optelling van <span class="font-bold">zwarte</span> fbm's
            | <span class="text-yellow-400 font-bold">gele rand:</span> aandachtspunt voor jou
        </span>
    </div>

    <table class="table-fixed border-collapse border border-gray-400 max-w-full min-w-full tabular-nums text-base">
        <thead class="bg-white shadow text-xs sm:text-lg">
            <tr>
                <x-table.th>Vak</x-table.th>
                <x-table.th class="hidden sm:block">Code</x-table.th>
                <x-table.th>Week</x-table.th>
                <x-table.th class="w-1/4 sm:max-w-md text-left overflow:hidden text-ellipsis">Titel</x-table.th>
                <x-table.th>Punten</x-table.th>
                <x-table.th class="w-1/4">Behaald</x-table.th>
            </tr>
        </thead>
        <tbody class="text-xs sm:text-lg">
            @foreach ($blok->vakken as $vak)
                @if($loop->first) <tr> @endif
                    <x-table.th zebra="{{ $loop->even }}" rowspan="{{ count($vak->feedbackmomenten) }}">{{ $vak->vak }}</x-table.th>
                    @foreach ($vak->feedbackmomenten as $feedbackmoment)
                        @if(!$loop->first) <tr> @endif
                            <x-table.thfbm studentView="true" :loop="$loop" :fbmsActive="$fbmsActive" :currentWeek="$currentWeek" :feedbackmoment="$feedbackmoment" class="font-mono hidden sm:block">{{ $feedbackmoment->code }}</x-table.thfbm>
                            <x-table.thfbm studentView="true" :loop="$loop" :fbmsActive="$fbmsActive" :currentWeek="$currentWeek" :feedbackmoment="$feedbackmoment">{{ str_pad($feedbackmoment->week, 2, "0", STR_PAD_LEFT) }}</x-table.thfbm>
                            <x-table.thfbm studentView="true" :loop="$loop" :fbmsActive="$fbmsActive" :currentWeek="$currentWeek" :feedbackmoment="$feedbackmoment" class="text-left overflow:hidden text-ellipsis sm:max-w-md font-normal text-xs sm:text-sm">{{ $feedbackmoment->naam }}</x-table.thfbm>
                            <x-table.thfbm studentView="true" :loop="$loop" :fbmsActive="$fbmsActive" :currentWeek="$currentWeek" :feedbackmoment="$feedbackmoment">{{ str_pad($feedbackmoment->points, 2, "0", STR_PAD_LEFT) }}</x-table.thfbm>
                            @if(isset($student->feedbackmomenten[$feedbackmoment->id]) && $fbmsActive->pluck('id')->contains($feedbackmoment->id))
                                <x-table.th zebra="{{ $loop->parent->even }}" red="{{ $student->feedbackmomenten[$feedbackmoment->id] < $feedbackmoment->points }}">
                                    {{ $student->feedbackmomenten[$feedbackmoment->id] }}
                                </x-table.th>
                            @elseif(isset($student->feedbackmomenten[$feedbackmoment->id]))
                                <x-table.th zebra="{{ $loop->parent->even }}" class="text-gray-300">
                                    {{ $student->feedbackmomenten[$feedbackmoment->id] }}
                                </x-table.th>
                            @elseif($fbmsActive->pluck('id')->contains($feedbackmoment->id))
                                <x-table.th zebra="{{ $loop->parent->even }}" class="border-yellow-400 border-2"></x-table.th>
                            @else
                                <x-table.th zebra="{{ $loop->parent->even }}"></x-table.th>
                            @endif
                         </tr>
                    @endforeach
            @endforeach
        </tbody>
    </table>

</x-layouts.app>
