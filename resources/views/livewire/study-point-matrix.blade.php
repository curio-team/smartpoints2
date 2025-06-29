<div x-data="{
        hoverRow: null,
        hoverColumn: null,
        changesMade: {},
    }"
    x-on:study-point-matrix-changed.window="changesMade = {}">
    @php $currentWeek = $this->blok->currentWeek ?? 0; @endphp

    <div class="flex flex-row items-center justify-between bg-gray-100 shadow p-2 px-4 sticky top-0 z-50 h-14">
        <div class="flex flex-row items-center gap-3">
            <div>
                <x-input.select wire:model.live="selectedGroupId" id="groupChanger" class="h-9">
                    <option disabled value="-1">Selecteer een klas</option>
                    @foreach ($groups as $group)
                        <option value="{{ $group['group_id'] }}">
                            {{ $group['name'] }}
                        </option>
                    @endforeach
                </x-input.select>
            </div>
            <div x-show="$wire.selectedBlokId != -1">
                <x-input.select wire:model.live="selectedBlokId" id="blokChanger" class="h-9">
                    <option value="-1">Huidig blok</option>
                    @foreach ($blokken as $blok)
                        <option value="{{ $blok->id }}" :selected="$wire.selectedBlokId == {{ $blok->id }}">
                            {{ $blok->blok }}
                        </option>
                    @endforeach
                </x-input.select>
            </div>
            <div x-show="$wire.isSpecialisatieBlok">
                <x-input.select wire:model.live="specialisatieFilter" id="specialisatieFilterChanger" class="h-9">
                    <option value="">Toon Alle Modules</option>
                    <option value="native">Toon geen WEB-modules</option>
                    <option value="web">Toon geen NATIVE-modules</option>
                </x-input.select>
            </div>
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
        </span>
    </div>

    @if(isset($this->blok))
        <div class="sticky top-[56px] z-50">
            <div class="overflow-auto syncscroll" name="syncTable">
                <x-matrix.grades-table-head :blok="$blok" :fbmsActive="$fbmsActive" :currentWeek="$currentWeek" />
            </div>
        </div>

        <form class="overflow-auto z-0 syncscroll" name="syncTable" wire:submit="save">
            {{-- This button is to make saving by enter key work: --}}
            <input type="submit" style="display: none;">

            <x-matrix.grades-table-body :blok="$blok" :students="$students" :fbmsActive="$fbmsActive" :currentWeek="$currentWeek" />
        </form>
    @endif

    <div id="loadingIndicator" wire:ignore>
        <div class="fixed z-[100] inset-0 grid place-content-center bg-gray-100 bg-opacity-75">
            <x-icon.loading class="w-10 h-10 text-gray-600 animate-spin" />
        </div>
    </div>

    <div class="modals">
        @if ($floodFillValue > -1)
        <x-modal.confirmation cancel="$wire.cancelFloodFill()">
            <x-slot name="title">Alles vullen</x-slot>

            <p>Weet je zeker dat je alle niet gevulde waardes bij <span class="font-semibold">{{ __(':feedbackmoment (:fb_code)', [
                'feedbackmoment' => $floodFillSubject->naam,
                'fb_code' => $floodFillSubject->code
            ]) }}</span> van de <span class="font-semibold">{{ $floodFillCount }}</span> studenten in deze klas wilt overschrijven met de waarde <span class="font-semibold">{{ $floodFillValue }}</span>?</p>
            <p class="mt-4"><span class="font-semibold">Je kunt deze actie niet ongedaan maken!</span></p>

            <x-slot name="footer">
                <x-button-icon icon="close" wire:click="cancelFloodFill" wire:loading.attr="disabled">Annuleren</x-button-icon>
                <x-button-icon icon="save" wire:click="doFloodFill" class="bg-red-500 hover:bg-red-600" wire:loading.attr="disabled">Vullen</x-button-icon>
            </x-slot>
        </x-modal.confirmation>
        @endif
    </div>

    <script>
        document.addEventListener('livewire:initialized', () => {
            const loading = document.getElementById('loadingIndicator');
            loading.setAttribute('wire:loading', '');

            Livewire.hook('morph.updated', ({ el, component }) => {
                syncscroll.reset();
            })
        });
    </script>
    <script src="/js/syncscroll.js"></script>
</div>
