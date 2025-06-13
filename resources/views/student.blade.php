<x-layouts.app class="text-base" :title="$student->name">
    {{-- Livewire handles injecting alpine for us, but in this Laravel controller we must handle it ourselves --}}
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/persist@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <?php $currentWeek = $blok->currentWeek ?? 0; ?>
    <div class="flex flex-col sm:flex-row items-center justify:center sm:justify-between bg-gray-100 shadow p-2 px-4 sticky top-0 z-50 sm:h-14 ">
        <div class="flex gap-2" x-data="{
            specialisatieFilter: $persist('{{ request()->get('specialisatie') }}'),
            goToFilter() {
                window.location = `${window.location.pathname}?specialisatie=${this.specialisatieFilter}`;
            }
        }" x-effect="if (specialisatieFilter && specialisatieFilter !== '' && !window.location.search.includes('specialisatie=' + specialisatieFilter)) goToFilter();">
            <div class="flex flex-row items-center gap-3 font-bold text-xs sm:text-xl">{{ $student->name }}</div>

            <?php
            $colorA = $colorB = 'bg-white';

            if($student->totalAverage >= 5.5) {
                $colorA = 'bg-green-100';
            } else {
                $colorA = 'bg-red-100';
            }
            ?>
            <div class="flex flex-row items-center text-xs sm:text-base px-2 py-1 gap-1 rounded bg-gray-200">
               Week {{$currentWeek}}
            </div>
            <x-input.select x-model="specialisatieFilter"
                x-on:change="goToFilter();">
                <option value="">Toon Alle Modules</option>
                <option value="native">Toon geen WEB-modules</option>
                <option value="web">Toon geen NATIVE-modules</option>
            </x-input.select>
        </div>

        <span class="ps-2 text-sm italic text-xs sm:text-sm text-clip overflow-hidden hidden sm:block">
            <span class="text-gray-300 font-bold">grijs:</span> fbm is in de toekomst
            | <span class="text-yellow-400 font-bold">gele rand:</span> aandachtspunt voor jou
        </span>
    </div>
    <table class="table-fixed border-collapse border border-gray-400 max-w-full min-w-full tabular-nums text-base">
        <thead class="bg-white shadow text-xs sm:text-base">
            <tr>
                <x-table.th>Vak</x-table.th>
                <x-table.th class="hidden sm:table-cell">Code</x-table.th>
                <x-table.th>Week</x-table.th>
                <x-table.th class="w-1/4 sm:max-w-md text-left overflow:hidden text-ellipsis">Titel</x-table.th>
                <x-table.th class="{{ $colorA }}">
                    <span>{{ locale_number_format($student->totalAverage, 1) }}</span>
                </x-table.th>
            </tr>
        </thead>
        <tbody class="text-xs sm:text-base">
            @foreach ($blok->vakken as $vak)
                @if($loop->first) <tr> @endif
                    <x-table.th zebra="{{ $loop->even }}" rowspan="{{ count($vak->feedbackmomenten) }}">{{ $vak->vak }}</x-table.th>
                    @foreach ($vak->feedbackmomenten as $feedbackmoment)
                        @if(!$loop->first) <tr> @endif
                            <x-table.thfbm studentView="true" :loop="$loop" :fbmsActive="$fbmsActive" :currentWeek="$currentWeek" :feedbackmoment="$feedbackmoment" class="font-mono hidden sm:table-cell">{{ $feedbackmoment->code }}</x-table.thfbm>
                            <x-table.thfbm studentView="true" :loop="$loop" :fbmsActive="$fbmsActive" :currentWeek="$currentWeek" :feedbackmoment="$feedbackmoment">{{ str_pad($feedbackmoment->week, 2, "0", STR_PAD_LEFT) }}</x-table.thfbm>
                            <x-table.thfbm studentView="true" :loop="$loop" :fbmsActive="$fbmsActive" :currentWeek="$currentWeek" :feedbackmoment="$feedbackmoment" class="text-left overflow:hidden text-ellipsis sm:truncate sm:max-w-md font-normal text-xs sm:text-sm" title="{{ $feedbackmoment->naam }}">{{ $feedbackmoment->naam }}</x-table.thfbm>
                            @if(isset($student->feedbackmomenten[$feedbackmoment->id]) && $fbmsActive->pluck('id')->contains($feedbackmoment->id))
                                <x-table.th zebra="{{ $loop->parent->even }}" red="{{ $student->feedbackmomenten[$feedbackmoment->id] < 5.5 }}">
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
