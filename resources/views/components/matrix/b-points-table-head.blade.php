<table class="table-auto border-collapse border border-gray-400">
    <thead class="bg-white shadow">
        <tr>
            <x-table.th disabled class="sticky left-0" style="min-width: 300px;"></x-table.th>
            @foreach ($this->blok->vakken as $vak)
                <?php $text = ($this->vakkenActiveB->contains($vak->uitvoer_id)) ? "" : "text-gray-400"; ?>
                <x-table.th style="min-width: 80px" zebra="{{ $loop->even }}" class="{{ $text }}">{{ $vak->vak }} </x-table.th>
            @endforeach
        </tr>
        <tr>
            <x-table.th disabled class="sticky left-0 italic text-sm text-right font-normal pe-2">punten:</x-table.th>
            @foreach ($this->blok->vakken as $vak)
                <?php $text = ($this->vakkenActiveB->contains($vak->uitvoer_id)) ? "" : "text-gray-400"; ?>
                <x-table.th class="text-sm" zebra="{{ $loop->even }}" class="{{ $text }}">2</x-table.th>
            @endforeach
        </tr>
    </thead>
</table>
