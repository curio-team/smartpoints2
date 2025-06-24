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
            <x-table.th disabled class="sticky left-0 italic text-sm text-right font-normal pe-2">
                {{-- acties: --}}
            </x-table.th>
            @foreach ($this->blok->vakken as $vak)
                @foreach ($vak->feedbackmomenten as $fbmKey => $feedbackmoment)
                    <x-table.thfbm :loop="$loop" :fbmsActive="$fbmsActive" :currentWeek="$currentWeek" :feedbackmoment="$feedbackmoment"
                    {{-- wire:click="startFloodFill({{ $feedbackmoment->id }})" --}}
                    >
                        <div class="flex flex-col gap-2 items-center justify-center">
                            {{-- <x-icon.flood-fill class="inline-block w-4 h-4 text-gray-500 hover:text-gray-700 cursor-pointer" /> --}}

                            @if ($feedbackmoment->points !== 100)
                                <span class="text-xs text-red-500">Fout! Punten {{ $feedbackmoment->points }} ipv 100!</span>
                            @endif
                        </div>
                    </x-table.thfbm>
                @endforeach
            @endforeach
        </tr>
    </thead>
</table>
