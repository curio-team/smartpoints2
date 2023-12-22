<div x-data="{
        showPoints: 'a',
        hoverRow: null,
        hoverColumn: null,
        changesMade: {},
    }"
    x-on:study-point-matrix-changed.window="changesMade = {}">
    <?php $currentWeek = \App\Models\SchoolWeek::getCurrentWeekNumber() ?? 0; ?>

    <div class="flex flex-row items-center justify-between bg-gray-100 shadow p-2 px-4 sticky top-0 z-50 h-14">
        <div class="flex flex-row items-center gap-3">
            <x-input.select wire:model.live="selectedGroupId" id="groupChanger" class="h-9"
                x-on:change="history.pushState({groupId: document.getElementById('groupChanger').value}, '', '/groups/' + document.getElementById('groupChanger').value)">
                <option disabled value="-1">Selecteer een klas</option>
                @foreach ($groups as $group)
                    <option value="{{ $group['group_id'] }}">{{ $group['name'] }}</option>
                @endforeach
            </x-input.select>

            <x-button-icon icon="save"
                x-cloak
                wire:click="save"
                class="relative h-9"
                x-show="Object.keys(changesMade).length > 0">
                <x-badge color="bg-red-500"
                    class="absolute -top-1 -right-1">
                    <span x-text="Object.keys(changesMade).length"></span>
                </x-badge>
                @lang('Save')
            </x-button-icon>
        </div>
        <span class="ps-2 text-sm italic">
            <span class="text-gray-300 font-bold">grijs:</span> fbm is in de toekomst
            | <span class="text-red-400 font-bold">rood:</span> voor hele klas nog niet ingevuld
            | totaal is een optelling van alle <span class="font-bold">zwarte</span> fbm's
        </span>
    </div>

    @if(isset($this->blok))
        <div class="sticky top-[56px] z-50">
            <div class="overflow-auto syncscroll" name="syncTable">
                <div x-show="showPoints == 'a'">
                    <x-matrix.a-points-table-head :blok="$blok" :fbmsActive="$fbmsActive" :currentWeek="$currentWeek" />
                </div>
                <div x-show="showPoints == 'b'">
                    <x-matrix.b-points-table-head :blok="$blok" :fbmsActive="$fbmsActive" :currentWeek="$currentWeek" />
                </div>
            </div>
        </div>

        <form class="overflow-auto z-0 syncscroll" name="syncTable" wire:submit="save">
            {{-- This button is to make saving by enter key work: --}}
            <input type="submit" style="display: none;">

            <div x-show="showPoints == 'a'">
                <x-matrix.a-points-table-body :blok="$blok" :students="$students" :fbmsActive="$fbmsActive" :currentWeek="$currentWeek" />
            </div>
            <div x-show="showPoints == 'b'">
                <x-matrix.b-points-table-body :blok="$blok" :students="$students" :fbmsActive="$fbmsActive" :currentWeek="$currentWeek" />
            </div>

        </form>
    @endif

    <div id="loadingIndicator" wire:ignore>
        <div class="fixed z-[100] inset-0 grid place-content-center bg-gray-100 bg-opacity-75">
            <x-icon.loading class="w-10 h-10 text-gray-600 animate-spin" />
        </div>
    </div>

    <script>
        document.addEventListener('livewire:initialized', () => {
            const loading = document.getElementById('loadingIndicator');
            loading.setAttribute('wire:loading', '');

            Livewire.hook('morph.updated', ({ el, component }) => {
                syncscroll.reset();
            })

            window.addEventListener("popstate", (event) => {
                if(event.state)
                {
                    @this.dispatch('new-group-id-from-state', {id: event.state.groupId});
                }
                else
                {
                    window.location = document.location;
                }
            });
        });
    </script>
    <script src="/js/syncscroll.js" />
</div>
