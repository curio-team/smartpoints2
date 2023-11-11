<div class="flex flex-col lg:flex-row p-4 gap-4">
    <section class="flex-shrink-0 p-4 border rounded">
        <h2 class="text-lg font-bold">Add School Week</h2>
        <div class="flex flex-col gap-2">
            <div class="flex flex-col gap-2">
                <div class="flex flex-row gap-2">
                    <label for="year_start">
                        Year Start - End
                    </label>
                    <x-input.text wire:model="year_start" id="year_start" type="number" class="flex-grow" />
                    -
                    <x-input.text wire:model="year_end" id="year_end" type="number" class="flex-grow" />
                </div>
                <div class="flex flex-row gap-2">
                    <label for="week_number">
                        Week Number
                    </label>
                    <x-input.text wire:model="week_number" id="week_number" type="number" class="flex-grow" />
                </div>
                <div class="flex flex-row gap-2">
                    <label for="date_of_monday">
                        Date of Monday
                    </label>
                    <x-input.text wire:model="date_of_monday" id="date_of_monday" type="date" class="flex-grow" />
                </div>
            </div>
            <div class="flex flex-col gap-2">
                <x-link wire:click="addWeek">Add</x-link>
                <x-link wire:click="resetFields">Reset</x-link>
            </div>
        </div>
    </section>
    <section class="flex-grow">
        <h2 class="text-lg font-bold">School Weeks</h2>
        <div class="flex flex-col gap-2">
            @foreach ($weeks->groupBy(fn($week) => $week['year_start'] . '-' . $week['year_end']) as $cohort => $cohortWeeks)
                <div class="flex flex-col gap-2 border rounded p-4" wire:key="cohort-{{ $cohort }}" x-data="{ weekChanged: false }">
                    <h3 class="text-md font-bold">{{ $cohort }}</h3>

                    @foreach ($cohortWeeks as $key => $week)
                        <div class="flex flex-col gap-2" wire:key="week-{{ $week['id'] }}">
                            <div class="flex flex-col gap-2">
                                <div class="flex flex-row gap-2">
                                    <x-input.text wire:model="weeks.{{ $key }}.year_start" type="number" class="flex-grow" x-on:input="weekChanged = true" />
                                    <x-input.text wire:model="weeks.{{ $key }}.year_end" type="number" class="flex-grow" x-on:input="weekChanged = true" />
                                </div>
                                <div class="flex flex-row gap-2">
                                    <x-input.text wire:model="weeks.{{ $key }}.week_number" type="number" class="flex-grow" x-on:input="weekChanged = true" />
                                    <x-input.text wire:model="weeks.{{ $key }}.date_of_monday" type="date" class="flex-grow" x-on:input="weekChanged = true" />
                                </div>
                            </div>
                            <div class="flex flex-row gap-2">
                                <x-link class="flex-grow" wire:click="updateWeek({{ $week['id'] }})" x-bind:aria-disabled="!weekChanged">Update</x-link>
                                <x-link class="flex-grow" wire:click="deleteWeek({{ $week['id'] }})">Delete</x-link>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </section>
</div>
