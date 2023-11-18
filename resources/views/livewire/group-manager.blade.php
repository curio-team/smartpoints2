<div class="p-4">
    <table class="w-full md:w-1/2 mx-auto">
        <tr>
            <x-table.th class="md:px-4 px-2 py-1 md:py-2 text-left">Groep</x-table.th>
            <x-table.th class="md:px-4 px-2 py-1 md:py-2">
                <div class="flex justify-between items-center">
                    Cohort
                    <x-button-icon icon="save" wire:click="save">opslaan</x-button-icon>
                </div>
            </x-table.th>
        </tr>
        @foreach($groups as $key => $group)
            <x-table.tr>
                <x-table.td zebra="{{ !$loop->even }}">{{ $group['name'] }}</x-table.td>
                <x-table.td zebra="{{ !$loop->even }}">
                    <x-input.select wire:model="groups.{{ $key }}.cohort">
                        <option value="-1">link met cohort</option>
                        @foreach($cohorts as $cohort)
                            <option value="{{ $cohort->id }}">{{ $cohort->naam }}</option>
                        @endforeach
                    </x-input.select>
                </x-table.td>
            </x-table.tr>
        @endforeach
    </table>

    <div id="loadingIndicator" wire:ignore wire:loading>
        <div class="absolute inset-0 grid place-content-center bg-gray-100 bg-opacity-75">
            <x-icon.loading class="w-10 h-10 text-gray-600 animate-spin" />
        </div>
    </div>
</div>