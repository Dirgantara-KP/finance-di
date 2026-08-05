<x-filament-panels::page>
    <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-800 dark:ring-white/10">
        <div class="p-6">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Tahun Anggaran <span class="text-red-500">*</span>
                    </label>
                    <x-filament::input.wrapper>
                        <x-filament::input.select wire:model.live="tahunAnggaran">
                            <option value="">-- Pilih Tahun --</option>
                            @foreach ($this->tahunOptions as $value)
                                <option value="{{ $value }}">{{ $value }}</option>
                            @endforeach
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Organisasi <span class="text-red-500">*</span>
                    </label>
                    <x-filament::input.wrapper :disabled="! $this->tahunAnggaran">
                        <x-filament::input.select wire:model.live="organisasi" :disabled="! $this->tahunAnggaran">
                            <option value="">-- Pilih Organisasi --</option>
                            @foreach ($this->organisasiOptions as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Sandi <span class="text-red-500">*</span>
                    </label>
                    <x-filament::input.wrapper>
                        <x-filament::input.select wire:model.live="sandi">
                            <option value="">-- Pilih Sandi --</option>
                            @foreach ($this->sandiOptions as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        PON <span class="text-red-500">*</span>
                    </label>
                    <x-filament::input.wrapper>
                        <x-filament::input.select wire:model.live="pon">
                            <option value="">-- Pilih PON --</option>
                            @foreach ($this->ponOptions as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        No. Kontrak <span class="text-red-500">*</span>
                    </label>
                    <x-filament::input.wrapper :disabled="! $this->organisasi">
                        <x-filament::input.select wire:model.live="kontrak" :disabled="! $this->organisasi">
                            <option value="">-- Pilih Kontrak --</option>
                            @foreach ($this->kontrakOptions as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                </div>
            </div>

            <div class="mt-4 flex justify-end">
                <x-filament::button
                    wire:click="loadData"
                    icon="heroicon-m-magnifying-glass"
                    :disabled="! $this->allFiltersSelected"
                >
                    Muat Data
                </x-filament::button>
            </div>
        </div>
    </div>

    @php
        $q1 = 'bg-blue-50/50 dark:bg-blue-900/10';
        $q2 = 'bg-emerald-50/50 dark:bg-emerald-900/10';
        $q3 = 'bg-amber-50/50 dark:bg-amber-900/10';
        $q4 = 'bg-violet-50/50 dark:bg-violet-900/10';
        $monthBg = [$q1, $q1, $q1, $q2, $q2, $q2, $q3, $q3, $q3, $q4, $q4, $q4];
        $months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agt','Sep','Okt','Nov','Des'];
    @endphp

    {{-- Single shared Alpine scope wraps the table AND the action buttons below,
         so Insert/Update can read the exact array the user just edited and send
         it in the SAME request as the action itself. No more racing $wire.set(). --}}
    <div
        x-data="{
            addMonth: {{ json_encode(array_map('intval', $addMonth)) }},
            saldoAwal: {{ json_encode(array_map('intval', $saldoAwal)) }},
            saldoAkhir: {{ json_encode(array_map('intval', $saldoAkhir)) }},
            baseAkhir: {{ json_encode(array_map('intval', $saldoAkhir)) }},
            cleanAddMonth: {{ json_encode(array_map('intval', $addMonth)) }},
            totals: { awal: {{ $totalSaldoAwal }}, add: {{ $totalPenambahan }}, akhir: {{ $totalSaldoAkhir }} },
            canInsert: {{ $this->canInsert ? 'true' : 'false' }},
            canUpdate: {{ $this->canUpdate ? 'true' : 'false' }},
            editing: {},
            savedMap: {},
            busy: false,
            get canEdit() { return (this.canInsert || this.canUpdate) && !this.busy; },
            get dirty() {
                for (let i = 0; i < 12; i++) {
                    if ((parseInt(this.addMonth[i]) || 0) !== (parseInt(this.cleanAddMonth[i]) || 0)) return true;
                }
                return false;
            },
            get displayedChange() { return this.dirty ? this.totals.add : this.lastChange; },
            init() { this.recalculate(); },
            lastChange: {{ (int) $lastAppliedChange }},
            recalculate() {
                let tAwal = 0, tAdd = 0, tAkhir = 0;
                for (let i = 0; i < 12; i++) {
                    const addVal = parseInt(this.addMonth[i]) || 0;
                    const baseVal = parseInt(this.baseAkhir[i]) || 0;
                    this.saldoAkhir[i] = baseVal + addVal;
                    tAwal += parseInt(this.saldoAwal[i]) || 0; tAdd += addVal; tAkhir += this.saldoAkhir[i];
                }
                this.totals.awal = tAwal; this.totals.add = tAdd; this.totals.akhir = tAkhir;
            },
            syncFromServer(d) {
                if (!d) return;
                this.saldoAwal = Array.from({ length: 12 }, (_, k) => parseInt(d.saldoAwal?.[k]) || 0);
                this.addMonth = Array.from({ length: 12 }, (_, k) => parseInt(d.addMonth?.[k]) || 0);
                this.saldoAkhir = Array.from({ length: 12 }, (_, k) => parseInt(d.saldoAkhir?.[k]) || 0);
                this.lastChange = parseInt(d.lastChange) || 0;
                this.baseAkhir = [...this.saldoAkhir];
                this.cleanAddMonth = [...this.addMonth];
                this.canInsert = !!(d.canInsert);
                this.canUpdate = !!(d.canUpdate);
                this.editing = {};
                this.savedMap = {};
                this.busy = false;
                this.recalculate();
            },
            format(n) { return (Number(n) || 0).toLocaleString('id-ID', { maximumFractionDigits: 0 }); },
            isNumKey(e) {
                const allowed = ['Backspace','Delete','Tab','Enter','Escape','ArrowLeft','ArrowRight','ArrowUp','ArrowDown','Home','End'];
                if (allowed.includes(e.key)) return true;
                if ((e.ctrlKey || e.metaKey) && ['a','c','v','x','z'].includes(e.key)) return true;
                return (e.key >= '0' && e.key <= '9') || e.key.startsWith('Numpad') || e.code?.startsWith('Numpad');
            },
            beginEdit(i) {
                if (!this.canEdit) return;
                this.savedMap[i] = this.addMonth[i];
                this.editing[i] = true;
                this.$nextTick(() => {
                    const el = this.$refs['i' + i];
                    el.focus();
                    el.setSelectionRange(el.value.length, el.value.length);
                });
            },
            cancelEdit(i) {
                this.addMonth[i] = this.savedMap[i] ?? 0;
                this.recalculate();
                this.editing[i] = false;
            },
            liveUpdate(i, rawVal) {
                const val = parseInt(String(rawVal).replace(/\D/g, '')) || 0;
                this.addMonth[i] = val;
                this.recalculate();
            },
            commitEdit(i, rawVal) {
                if (!this.editing[i]) return;
                const val = parseInt(String(rawVal).replace(/\D/g, '')) || 0;
                this.addMonth[i] = val;
                this.recalculate();
                this.editing[i] = false;
            },
            submitInsert() {
                if (!this.canInsert || this.busy) return;
                this.busy = true;
                $wire.insert([...this.addMonth]).catch(() => { this.busy = false; });
            },
            submitUpdate() {
                if (!this.canUpdate || !this.dirty || this.busy) return;
                this.busy = true;
                $wire.update([...this.addMonth]).catch(() => { this.busy = false; });
            }
        }"
        x-init="syncFromServer({
            saldoAwal: {{ json_encode(array_map('intval', $saldoAwal)) }},
            addMonth: {{ json_encode(array_map('intval', $addMonth)) }},
            saldoAkhir: {{ json_encode(array_map('intval', $saldoAkhir)) }},
            canInsert: {{ $this->canInsert ? 'true' : 'false' }},
            canUpdate: {{ $this->canUpdate ? 'true' : 'false' }}
        })"
        @plafond-data-loaded.window="syncFromServer($event.detail)"
    >
        <div class="mt-6 rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-800 dark:ring-white/10">
            <div class="overflow-x-auto">
                <table class="w-full min-w-300">
                    <thead class="bg-gray-50 dark:bg-white/5">
                        <tr class="border-b border-gray-200 dark:border-white/10">
                            <th class="w-12 px-3 py-3.5 text-center text-sm font-semibold text-gray-950 dark:text-white">No</th>
                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-950 dark:text-white">Uraian</th>
                            @foreach ($months as $i => $label)
                                <th class="px-3 py-3.5 text-right text-sm font-semibold text-gray-950 dark:text-white min-w-25 {{ $monthBg[$i] }}">{{ $label }}</th>
                            @endforeach
                            <th class="px-3 py-3.5 text-right text-sm font-semibold text-gray-950 dark:text-white">Total</th>
                        </tr>
                    </thead>
                    <tbody wire:key="plafond-tbody-{{ $dataLoaded ? 'loaded' : 'empty' }}">
                        <tr class="even:bg-gray-50 dark:even:bg-white/5">
                            <td class="px-3 py-3 text-center text-sm text-gray-500 dark:text-gray-400">1</td>
                            <td class="px-3 py-3 text-sm text-gray-950 dark:text-white">Saldo Awal</td>
                            @foreach ($saldoAwal as $i => $val)
                                <td class="px-3 py-3 text-right text-sm tabular-nums text-gray-950 dark:text-white min-w-25 {{ $monthBg[$i] }}" x-text="format(saldoAwal[{{ $i }}])">{{ number_format($val, 0, ',', '.') }}</td>
                            @endforeach
                            <td class="px-3 py-3 text-right text-sm font-semibold tabular-nums text-gray-950 dark:text-white" x-text="format(totals.awal)">{{ number_format($totalSaldoAwal, 0, ',', '.') }}</td>
                        </tr>

                        <tr class="even:bg-gray-50 dark:even:bg-white/5">
                            <td class="px-3 py-3 text-center text-sm text-gray-500 dark:text-gray-400">2</td>
                            <td class="px-3 py-3 text-sm text-gray-950 dark:text-white">Penambahan</td>
                            @for ($i = 0; $i < 12; $i++)
                                <td class="px-3 py-3 text-right text-sm tabular-nums min-w-25 {{ $monthBg[$i] }}
                                            {{ ($this->canInsert || $this->canUpdate) ? 'text-gray-950 dark:text-white' : 'text-gray-400 dark:text-gray-500 cursor-not-allowed' }}"
                                    :class="{ 'border-2 border-blue-500': editing[{{ $i }}], 'opacity-50 cursor-wait': busy }"
                                    @dblclick="beginEdit({{ $i }})">

                                    <span x-show="!editing[{{ $i }}]"
                                          x-text="format(addMonth[{{ $i }}])"
                                          class="block leading-5 select-none">
                                    </span>

                                    <input x-show="editing[{{ $i }}]"
                                           x-ref="i{{ $i }}"
                                           type="text"
                                           :value="savedMap[{{ $i }}] ?? 0"
                                           @input="liveUpdate({{ $i }}, $el.value)"
                                           @keydown="if (!isNumKey($event)) $event.preventDefault()"
                                           @keydown.escape.prevent="cancelEdit({{ $i }})"
                                           @blur="commitEdit({{ $i }}, $el.value)"
                                           class="w-full text-right outline-none border-0 p-0 bg-transparent leading-5"
                                           inputmode="numeric">
                                </td>
                            @endfor
                            <td class="px-3 py-3 text-right text-sm font-semibold tabular-nums text-gray-950 dark:text-white leading-5" x-text="format(totals.add)">{{ number_format($totalPenambahan, 0, ',', '.') }}</td>
                        </tr>

                        <tr class="even:bg-gray-50 dark:even:bg-white/5">
                            <td class="px-3 py-3 text-center text-sm text-gray-500 dark:text-gray-400">3</td>
                            <td class="px-3 py-3 text-sm text-gray-950 dark:text-white">Saldo Akhir</td>
                            @for ($i = 0; $i < 12; $i++)
                                <td class="px-3 py-3 text-right text-sm tabular-nums text-gray-950 dark:text-white min-w-25 {{ $monthBg[$i] }}" x-text="format(saldoAkhir[{{ $i }}])">{{ number_format($saldoAkhir[$i], 0, ',', '.') }}</td>
                            @endfor
                            <td class="px-3 py-3 text-right text-sm font-semibold tabular-nums text-gray-950 dark:text-white" x-text="format(totals.akhir)">{{ number_format($totalSaldoAkhir, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        @php($ringkasan = $this->getRingkasan())
        <div class="mt-6 rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-800 dark:ring-white/10">
            <div class="border-b border-gray-200 px-6 py-4 dark:border-white/10">
                <h3 class="text-base font-semibold text-gray-950 dark:text-white">Ringkasan Setelah Update</h3>
            </div>

            <div class="grid grid-cols-1 gap-4 p-6 sm:grid-cols-3">
                <div class="flex items-start gap-3 rounded-lg bg-green-50 p-4 dark:bg-green-500/10">
                    <x-filament::icon
                        icon="heroicon-o-document-check"
                        class="h-8 w-8 shrink-0 text-green-600 dark:text-green-400"
                    />
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Total Saldo Akhir Baru</p>
                        <p class="text-lg font-semibold tabular-nums text-gray-950 dark:text-white">
                            {{ number_format($ringkasan['saldo_akhir_baru'], 0, ',', '.') }}
                        </p>
                    </div>
                </div>

                <div class="flex items-start gap-3 rounded-lg bg-gray-50 p-4 dark:bg-white/5">
                    <x-filament::icon
                        icon="heroicon-o-arrow-trending-up"
                        class="h-8 w-8 shrink-0 text-gray-500 dark:text-gray-400"
                    />
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Perubahan Total</p>
                        <p class="text-lg font-semibold tabular-nums text-gray-950 dark:text-white" x-text="format(totals.akhir - totals.awal)">
                            {{ number_format($ringkasan['perubahan_total'], 0, ',', '.') }}
                        </p>
                    </div>
                </div>

                <div class="flex items-start gap-3 rounded-lg bg-gray-50 p-4 dark:bg-white/5">
                    <x-filament::icon
                        icon="heroicon-o-banknotes"
                        class="h-8 w-8 shrink-0 text-gray-500 dark:text-gray-400"
                    />
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Total Saldo Awal</p>
                        <p class="text-lg font-semibold tabular-nums text-gray-950 dark:text-white">
                            {{ number_format($ringkasan['saldo_awal'], 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-2 gap-3 sm:flex sm:flex-wrap">
            <x-filament::button
                color="success"
                icon="heroicon-m-document-arrow-down"
                :disabled="! $this->dataLoaded"
                wire:click="exportExcel"
                class="justify-center"
            >
                Export To Excel
            </x-filament::button>

            <x-filament::button
                color="primary"
                icon="heroicon-m-plus"
                x-bind:disabled="!canInsert || busy"
                x-on:click="submitInsert()"
                class="justify-center"
            >
                <span x-show="!busy">Insert</span>
                <span x-show="busy" x-cloak>Menyimpan...</span>
            </x-filament::button>

            <x-filament::button
                color="warning"
                icon="heroicon-m-pencil-square"
                x-on:click="submitUpdate()"
                x-effect="$el.disabled = !canUpdate || !dirty || busy"
                class="justify-center"
            >
                <span x-show="!busy">Update</span>
                <span x-show="busy" x-cloak>Menyimpan...</span>
            </x-filament::button>

            <x-filament::button
                color="gray"
                icon="heroicon-m-arrow-uturn-left"
                wire:click="cancel"
                class="justify-center"
            >
                Cancel
            </x-filament::button>

            <x-filament::button
                color="gray"
                icon="heroicon-m-x-mark"
                wire:click="close"
                class="justify-center"
            >
                Close
            </x-filament::button>
        </div>
    </div>
    <x-filament-actions::modals />
</x-filament-panels::page>
