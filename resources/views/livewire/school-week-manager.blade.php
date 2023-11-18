<div class="flex flex-col lg:flex-row p-4 gap-4">
    <section>
        <h2 class="text-lg font-bold">Week toevoegen</h2>
        <div class="flex-shrink-0 p-4 border rounded">
            <div class="flex flex-col gap-2">
                <div class="flex flex-col gap-2">
                    <div class="flex flex-row gap-2">
                        <label for="year_start">
                            Jaar Start - Eind
                        </label>
                        <x-input.text wire:model="year_start" id="year_start" type="number" class="flex-grow" />
                        -
                        <x-input.text wire:model="year_end" id="year_end" type="number" class="flex-grow" />
                    </div>
                    <div class="flex flex-row gap-2">
                        <label for="week_number">
                            Weeknummer
                        </label>
                        <x-input.text wire:model="week_number" id="week_number" type="number" class="flex-grow" />
                    </div>
                    <div class="flex flex-row gap-2">
                        <label for="date_of_monday">
                            Maandag
                        </label>
                        <x-input.text wire:model="date_of_monday" id="date_of_monday" type="date" class="flex-grow" />
                    </div>
                </div>
                <div class="flex flex-col gap-2">
                    <x-button-icon icon="save" wire:click="addWeek">Toevoegen</x-button-icon>
                    <x-button-icon icon="loading" wire:click="resetFields">Reset</x-button-icon>
                </div>
            </div>
        </div>
    </section>
    <section class="flex-grow">
        <h2 class="text-lg font-bold">Schoolweken</h2>
        <div class="flex flex-col gap-2">
            @foreach ($weeks->groupBy(fn($week) => $week['year_start'] . '-' . $week['year_end'], preserveKeys: true) as $cohort => $cohortWeeks)
                <div class="flex flex-col gap-2 border rounded p-4" wire:key="cohort-{{ $cohort }}">
                    <h3 class="text-md font-bold">{{ $cohort }}</h3>
                    @foreach ($cohortWeeks as $key => $week)
                        <div class="flex gap-2 items-center" wire:key="week-{{ $week['id'] }}" x-data="{ weekChanged: false }">
                            Week: 
                            <x-input.text wire:model="weeks.{{ $key }}.week_number" type="number" class="flex-grow" x-on:input="weekChanged = true" />
                            <x-input.text wire:model="weeks.{{ $key }}.date_of_monday" type="date" class="flex-grow" x-on:input="weekChanged = true" />
                            <x-button-icon icon="save" class="flex-grow text-black bg-blue-300 text-sm aria-disabled:bg-slate-100" wire:click="updateWeek({{ $week['id'] }})" x-bind:aria-disabled="!weekChanged">Aanpassen</x-link>
                            <x-button-icon icon="close" class="flex-grow text-black bg-red-200 text-sm" wire:click="deleteWeek({{ $week['id'] }})">Verwijderen</x-link>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </section>
</div>
