<table class="table-auto border-collapse border border-gray-400">
    <thead class="bg-white shadow">
        <tr>
            <x-table.th disabled class="sticky left-0" style="min-width: 300px;">
                <label for="">Selecteer punt type</label>
                <x-matrix.points-select></x-points-select>
            </x-table.th>
            @foreach ($blok->vakken as $vak)
                <x-table.th style="min-width: 80px" zebra="{{ $loop->even }}" colspan="{{ count($vak->feedbackmomenten) }}">{{ $vak->vak }} </x-table.th>
            @endforeach
        </tr>


        <tr>
            <x-table.th disabled class="sticky left-0 italic text-sm text-right font-normal pe-2">punten:</x-table.th>
            @foreach ($blok->vakken as $vak)
                <x-table.th colspan="{{ count($vak->feedbackmomenten) }}" style="min-width: 50px;" class="text-sm">2</x-table.th>
            @endforeach
        </tr>
    </thead>
</table>
