<x-layouts.app>

    <?php $currentWeek = \App\Models\SchoolWeek::getCurrentWeekNumber() ?? 0; ?>
    <div class="flex flex-row items-center justify-between bg-gray-100 shadow p-2 px-4 sticky top-0 z-50 h-14">
        <div class="flex gap-2">
            <div class="flex flex-row items-center gap-3 font-bold">{{ $student->name }}</div>
            
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
            <div class="flex flex-row items-center h-full px-2 py-1 gap-1 rounded {{ $color }}">
                {{ $student->totalPoints }} / {{ $student->totalPointsToGainUntilNow }}
            </div>
        </div>
        <span class="ps-2 text-sm italic">
            <span class="text-gray-300 font-bold">grijs:</span> dit fbm is in de toekomst
            | <span class="text-red-400 font-bold">rood:</span> dit fbm is bij niemand ingevuld maar de week is voorbij
            | totaal is een optelling van alle <span class="font-bold">zwarte</span> fbm's
        </span>
    </div>

    <table class="table-auto border-collapse border border-gray-400 max-w-full min-w-full tabular-nums">
        <thead class="bg-white shadow">
            <tr>
                <x-table.th>Vak</x-table.th>
                <x-table.th>Code</x-table.th>
                <x-table.th>Week</x-table.th>
                <x-table.th class="max-w-md">Titel</x-table.th>
                <x-table.th>Punten</x-table.th>
                <x-table.th class="w-1/4">Behaald</x-table.th>
            </tr>
        </thead>
        <tbody>
            @foreach ($blok->vakken as $vak)
                @if($loop->first) <tr> @endif
                    <x-table.th zebra="{{ $loop->even }}" rowspan="{{ count($vak->feedbackmomenten) }}">{{ $vak->vak }}</x-table.th>
                    @foreach ($vak->feedbackmomenten as $feedbackmoment)
                        @if(!$loop->first) <tr> @endif 
                            <x-table.thfbm :loop="$loop" :fbmsActive="$fbmsActive" :currentWeek="$currentWeek" :feedbackmoment="$feedbackmoment" class="font-mono">{{ $feedbackmoment->code }}</x-table.thfbm>
                            <x-table.thfbm :loop="$loop" :fbmsActive="$fbmsActive" :currentWeek="$currentWeek" :feedbackmoment="$feedbackmoment">{{ str_pad($feedbackmoment->week, 2, "0", STR_PAD_LEFT) }}</x-table.thfbm>
                            <x-table.thfbm :loop="$loop" :fbmsActive="$fbmsActive" :currentWeek="$currentWeek" :feedbackmoment="$feedbackmoment" class="text-left truncate max-w-md font-normal text-sm">{{ $feedbackmoment->naam }}</x-table.thfbm>
                            <x-table.thfbm :loop="$loop" :fbmsActive="$fbmsActive" :currentWeek="$currentWeek" :feedbackmoment="$feedbackmoment">{{ str_pad($feedbackmoment->points, 2, "0", STR_PAD_LEFT) }}</x-table.thfbm>
                            @if(isset($student->feedbackmomenten[$feedbackmoment->id]))
                                <x-table.th zebra="{{ $loop->parent->even }}" red="{{ $student->feedbackmomenten[$feedbackmoment->id] < $feedbackmoment->points }}">
                                    {{ $student->feedbackmomenten[$feedbackmoment->id] }}
                                </x-table.th>
                            @elseif($fbmsActive->pluck('id')->contains($feedbackmoment->id))
                                <x-table.th zebra="{{ $loop->parent->even }}" class="border-yellow-500 border-2"></x-table.th>
                            @else
                                <x-table.th zebra="{{ $loop->parent->even }}"></x-table.th>
                            @endif
                        @if(!$loop->first) </tr> @endif 
                    @endforeach
                @if($loop->first) </tr> @endif
            @endforeach
        </tbody>
    </table>

</x-layouts.app>