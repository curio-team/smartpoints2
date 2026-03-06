<x-layouts.app title="Verweesde scores">
    {{-- Livewire handles injecting alpine for us, but in this Laravel controller we must handle it ourselves --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <div class="p-4 md:p-8 max-w-4xl mx-auto" x-data="{
        loading: true,
        error: null,
        orphanedGroups: [],
        orphans: [],
        selectedGroups: [],
        selectedStudents: [],

        get allGroupsSelected() {
            return this.orphanedGroups.length > 0 && this.orphanedGroups.every(g => this.selectedGroups.includes(String(g.group_id)));
        },
        get allStudentsSelected() {
            return this.orphans.length > 0 && this.orphans.every(s => this.selectedStudents.includes(String(s.student_id)));
        },
        toggleAllGroups(e) {
            this.selectedGroups = e.target.checked ? this.orphanedGroups.map(g => String(g.group_id)) : [];
        },
        toggleAllStudents(e) {
            this.selectedStudents = e.target.checked ? this.orphans.map(s => String(s.student_id)) : [];
        },

        async init() {
            try {
                const res = await fetch('{{ route('orphaned-scores.data') }}');
                if (!res.ok) throw new Error('HTTP ' + res.status);
                const data = await res.json();
                this.orphanedGroups = data.orphanedGroups;
                this.orphans = data.orphans;
            } catch (e) {
                this.error = e.message;
            } finally {
                this.loading = false;
            }
        }
    }" x-init="init()">

        <h1 class="text-2xl font-bold mb-2">Verweesde scores</h1>

        @if(session('success'))
            <div class="mb-4 px-4 py-3 bg-green-100 border border-green-300 text-green-800 rounded">
                {{ session('success') }}
            </div>
        @endif

        <div x-show="loading" class="py-10 text-center text-gray-500 text-sm">
            <svg class="animate-spin inline w-5 h-5 mr-2 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
            </svg>
            Bezig met ophalen van API-data…
        </div>

        <div x-show="error" class="py-4 px-4 bg-red-100 border border-red-300 text-red-800 rounded text-sm" x-text="'Fout bij laden: ' + error"></div>

        <template x-if="!loading && !error">
            <div>
                {{-- Orphaned groups --}}
                <h2 class="text-lg font-semibold mt-6 mb-1">Groepen die niet meer bestaan in de API</h2>
                <p class="text-gray-600 mb-3 text-sm">
                    Deze groepen staan nog in de lokale database maar bestaan niet meer in de API.
                </p>

                <template x-if="orphanedGroups.length === 0">
                    <div class="px-4 py-4 bg-gray-50 border border-gray-200 rounded text-center text-gray-500 text-sm">
                        Geen verweesde groepen gevonden.
                    </div>
                </template>

                <template x-if="orphanedGroups.length > 0">
                    <form method="POST" action="{{ route('orphaned-groups.bulk-destroy') }}"
                        x-on:submit.prevent="selectedGroups.length > 0 && confirm('Weet je zeker dat je ' + selectedGroups.length + ' groep(en) wilt verwijderen?') && $el.submit()">
                        @csrf
                        <template x-for="id in selectedGroups" :key="id">
                            <input type="hidden" name="group_ids[]" :value="id">
                        </template>
                        <table class="w-full border-collapse border border-gray-400 text-sm mb-1">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="border px-1 py-1 md:py-2 w-8 text-center">
                                        <input type="checkbox" :checked="allGroupsSelected" @change="toggleAllGroups($event)">
                                    </th>
                                    <th class="border px-1 py-1 md:py-2">Groep ID (API)</th>
                                    <th class="border px-1 py-1 md:py-2">Cohort ID</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="group in orphanedGroups" :key="group.group_id">
                                    <tr class="odd:bg-white even:bg-gray-50">
                                        <td class="border px-2 py-1 text-center">
                                            <input type="checkbox" :value="String(group.group_id)" x-model="selectedGroups">
                                        </td>
                                        <td class="border px-2 py-1 font-mono" x-text="group.group_id"></td>
                                        <td class="border px-2 py-1" x-text="group.cohort_id"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                        <div class="flex items-center gap-3 mt-2 mb-6">
                            <button type="submit" :disabled="selectedGroups.length === 0"
                                class="px-4 py-1.5 text-sm bg-red-600 hover:bg-red-700 disabled:opacity-40 disabled:cursor-not-allowed text-white rounded">
                                Verwijder geselecteerde (<span x-text="selectedGroups.length">0</span>)
                            </button>
                        </div>
                    </form>
                </template>

                {{-- Orphaned student scores --}}
                <h2 class="text-lg font-semibold mt-6 mb-1">Studenten die niet meer actief zijn</h2>
                <p class="text-gray-600 mb-3 text-sm">
                    Dit zijn studenten die scores hebben in de database maar niet meer voorkomen in een actieve klas in de API.
                </p>

                <template x-if="orphans.length === 0">
                    <div class="px-4 py-4 bg-gray-50 border border-gray-200 rounded text-center text-gray-500 text-sm">
                        Geen verweesde scores gevonden.
                    </div>
                </template>

                <template x-if="orphans.length > 0">
                    <form method="POST" action="{{ route('orphaned-scores.bulk-destroy') }}"
                        x-on:submit.prevent="selectedStudents.length > 0 && confirm('Weet je zeker dat je scores van ' + selectedStudents.length + ' student(en) wilt verwijderen?') && $el.submit()">
                        @csrf
                        <template x-for="id in selectedStudents" :key="id">
                            <input type="hidden" name="student_ids[]" :value="id">
                        </template>
                        <table class="w-full border-collapse border border-gray-400 text-sm mb-1">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="border px-1 py-1 md:py-2 w-8 text-center">
                                        <input type="checkbox" :checked="allStudentsSelected" @change="toggleAllStudents($event)">
                                    </th>
                                    <th class="border px-1 py-1 md:py-2">Naam</th>
                                    <th class="border px-1 py-1 md:py-2">Student ID</th>
                                    <th class="border px-1 py-1 md:py-2 text-center">Scores (A)</th>
                                    <th class="border px-1 py-1 md:py-2 text-center">Scores (B)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="orphan in orphans" :key="orphan.student_id">
                                    <tr class="odd:bg-white even:bg-gray-50">
                                        <td class="border px-2 py-1 text-center">
                                            <input type="checkbox" :value="String(orphan.student_id)" x-model="selectedStudents">
                                        </td>
                                        <td class="border px-2 py-1" x-text="orphan.name"></td>
                                        <td class="border px-2 py-1 font-mono text-xs text-gray-500" x-text="orphan.student_id"></td>
                                        <td class="border px-2 py-1 text-center" x-text="orphan.count_a"></td>
                                        <td class="border px-2 py-1 text-center" x-text="orphan.count_b"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                        <div class="flex items-center gap-3 mt-2">
                            <button type="submit" :disabled="selectedStudents.length === 0"
                                class="px-4 py-1.5 text-sm bg-red-600 hover:bg-red-700 disabled:opacity-40 disabled:cursor-not-allowed text-white rounded">
                                Verwijder geselecteerde (<span x-text="selectedStudents.length">0</span>)
                            </button>
                        </div>
                    </form>
                </template>
            </div>
        </template>
    </div>
</x-layouts.app>
