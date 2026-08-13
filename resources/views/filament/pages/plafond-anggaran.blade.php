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

            <div class="mt-4 flex items-center justify-between">
                {{-- Petunjuk singkat saat filter belum lengkap, biar user tahu langkah selanjutnya --}}
                @unless ($this->allFiltersSelected)
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Lengkapi seluruh filter di atas untuk memuat data.
                    </p>
                @else
                    <span></span>
                @endunless

                <x-filament::button
                    wire:click="loadData"
                    wire:loading.attr="disabled"
                    wire:target="loadData"
                    icon="heroicon-m-magnifying-glass"
                    :disabled="! $this->allFiltersSelected"
                >
                    <span wire:loading.remove wire:target="loadData">Muat Data</span>
                    <span wire:loading wire:target="loadData">Memuat...</span>
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

    <div
        x-data="{
            addMonth: {{ json_encode(array_map('intval', $addMonth)) }},
            saldoAwal: {{ json_encode(array_map('intval', $saldoAwal)) }},
            saldoAkhir: {{ json_encode(array_map('intval', $saldoAkhir)) }},
            // ponytail: baseSaldoAkhir = server-computed awal + stored cumulative additions.
            // addMonth is the editable delta buffer (0 after load). Recalculate adds delta
            // on top so the historical additions aren't wiped when the form buffer is zero.
            baseSaldoAkhir: {{ json_encode(array_map('intval', $saldoAkhir)) }},
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
            init() { this.recalculate(); },
            recalculate() {
                let tAwal = 0, tAdd = 0, tAkhir = 0;
                for (let i = 0; i < 12; i++) {
                    const baseVal = parseInt(this.baseSaldoAkhir[i]) || 0;
                    const addVal = parseInt(this.addMonth[i]) || 0;
                    this.saldoAkhir[i] = baseVal + addVal;
                    tAwal += parseInt(this.saldoAwal[i]) || 0; tAdd += addVal; tAkhir += this.saldoAkhir[i];
                }
                this.totals.awal = tAwal; this.totals.add = tAdd; this.totals.akhir = tAkhir;
            },
            syncFromServer(d) {
                if (!d) return;
                this.saldoAwal = Array.from({ length: 12 }, (_, k) => parseInt(d.saldoAwal?.[k]) || 0);
                this.addMonth = Array.from({ length: 12 }, (_, k) => parseInt(d.addMonth?.[k]) || 0);
                this.baseSaldoAkhir = Array.from({ length: 12 }, (_, k) => parseInt(d.saldoAkhir?.[k]) || 0);
                this.saldoAkhir = [...this.baseSaldoAkhir];
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
                $wire.insert([...this.addMonth]).finally(() => { this.busy = false; });
            },
            submitUpdate() {
                if (!this.canUpdate || !this.dirty || this.busy) return;
                this.busy = true;
                $wire.update([...this.addMonth]).finally(() => { this.busy = false; });
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
        <div class="relative mt-6 rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-800 dark:ring-white/10">

            {{-- Overlay saat proses simpan/update berjalan --}}
            <div
                x-show="busy"
                x-transition.opacity
                x-cloak
                class="absolute inset-0 z-20 flex items-center justify-center rounded-xl bg-white/60 backdrop-blur-[1px] dark:bg-gray-800/60"
            >
                <div class="flex items-center gap-2 rounded-lg bg-white px-4 py-2 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-800 dark:ring-white/10">
                    <svg class="h-4 w-4 animate-spin text-primary-600" viewBox="0 0 24 24" fill="none">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                    </svg>
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Menyimpan data...</span>
                </div>
            </div>

            @if (! $dataLoaded)
                {{-- Empty state: belum ada data dimuat --}}
                <div class="flex flex-col items-center justify-center gap-2 px-6 py-16 text-center">
                    <x-filament::icon
                        icon="heroicon-o-table-cells"
                        class="h-10 w-10 text-gray-300 dark:text-gray-600"
                    />
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Belum ada data dimuat</p>
                    <p class="max-w-sm text-sm text-gray-400 dark:text-gray-500">
                        Pilih Tahun Anggaran, Organisasi, Sandi, PON, dan No. Kontrak, lalu klik "Muat Data".
                    </p>
                </div>
            @else
                <div class="overflow-x-auto rounded-xl">
                    <table class="w-full min-w-300 border-separate border-spacing-0">
                        <thead class="sticky top-0 z-10 bg-gray-50 dark:bg-gray-800">
                            <tr class="border-b border-gray-200 dark:border-white/10">
                                <th class="w-12 whitespace-nowrap bg-gray-50 px-3 py-3.5 text-center text-sm font-semibold text-gray-950 dark:bg-gray-800 dark:text-white">No</th>
                                <th class="sticky left-0 z-10 whitespace-nowrap bg-gray-50 px-3 py-3.5 text-left text-sm font-semibold text-gray-950 dark:bg-gray-800 dark:text-white">Uraian</th>
                                @foreach ($months as $i => $label)
                                    <th class="px-3 py-3.5 text-right text-sm font-semibold text-gray-950 dark:text-white min-w-25 {{ $monthBg[$i] }}">{{ $label }}</th>
                                @endforeach
                                <th class="bg-gray-50 px-3 py-3.5 text-right text-sm font-semibold text-gray-950 dark:bg-gray-800 dark:text-white">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="even:bg-gray-50 dark:even:bg-white/5">
                                <td class="px-3 py-3 text-center text-sm text-gray-500 dark:text-gray-400">1</td>
                                <td class="sticky left-0 z-[5] bg-white px-3 py-3 text-sm text-gray-950 dark:bg-gray-800 dark:text-white">Saldo Awal</td>
                                @foreach ($saldoAwal as $i => $val)
                                    <td class="px-3 py-3 text-right text-sm tabular-nums text-gray-950 dark:text-white min-w-25 {{ $monthBg[$i] }}" x-text="format(saldoAwal[{{ $i }}])">{{ number_format($val, 0, ',', '.') }}</td>
                                @endforeach
                                <td class="px-3 py-3 text-right text-sm font-semibold tabular-nums text-gray-950 dark:text-white" x-text="format(totals.awal)">{{ number_format($totalSaldoAwal, 0, ',', '.') }}</td>
                            </tr>

                            <tr class="even:bg-gray-50 dark:even:bg-white/5">
                                <td class="px-3 py-3 text-center text-sm text-gray-500 dark:text-gray-400">2</td>
                                <td class="sticky left-0 z-[5] bg-white px-3 py-3 text-sm text-gray-950 dark:bg-gray-800 dark:text-white">
                                    <span class="inline-flex items-center gap-1.5">
                                        Penambahan
                                        @if ($this->canInsert || $this->canUpdate)
                                            <x-filament::icon
                                                icon="heroicon-o-pencil-square"
                                                class="h-3.5 w-3.5 text-gray-400 dark:text-gray-500"
                                            />
                                        @endif
                                    </span>
                                </td>
                                @for ($i = 0; $i < 12; $i++)
                                    <td
                                        wire:key="penambahan-cell-{{ $i }}"
                                        title="{{ ($this->canInsert || $this->canUpdate) ? 'Klik dua kali untuk mengedit' : 'Kolom ini tidak dapat diedit' }}"
                                        class="relative select-none px-3 py-3 text-right text-sm tabular-nums min-w-25 transition-colors duration-150 {{ $monthBg[$i] }}
                                                {{ ($this->canInsert || $this->canUpdate)
                                                    ? 'text-gray-950 dark:text-white cursor-pointer hover:bg-primary-50 dark:hover:bg-primary-500/10'
                                                    : 'text-gray-400 dark:text-gray-500 cursor-not-allowed' }}"
                                        :class="{
                                            '!ring-2 !ring-inset !ring-primary-500 !bg-primary-50/80 dark:!bg-primary-500/10': editing[{{ $i }}],
                                            'pointer-events-none opacity-60': busy,
                                        }"
                                        @dblclick="beginEdit({{ $i }})">

                                        <span x-show="!editing[{{ $i }}]"
                                              x-text="format(addMonth[{{ $i }}])"
                                              class="block leading-5">
                                        </span>

                                        <input x-show="editing[{{ $i }}]"
                                               x-ref="i{{ $i }}"
                                               type="text"
                                               :value="savedMap[{{ $i }}] ?? 0"
                                               @keydown="if (!isNumKey($event)) $event.preventDefault()"
                                               @keydown.escape.prevent="cancelEdit({{ $i }})"
                                               @keydown.enter.prevent="$el.blur()"
                                               @blur="commitEdit({{ $i }}, $el.value)"
                                               class="w-full rounded-none border-0 bg-transparent p-0 text-right font-medium leading-5 text-primary-700 outline-none dark:text-primary-300"
                                               inputmode="numeric">
                                    </td>
                                @endfor
                                <td class="px-3 py-3 text-right text-sm font-semibold tabular-nums text-gray-950 dark:text-white leading-5" x-text="format(totals.add)">{{ number_format($totalPenambahan, 0, ',', '.') }}</td>
                            </tr>

                            <tr class="even:bg-gray-50 dark:even:bg-white/5">
                                <td class="px-3 py-3 text-center text-sm text-gray-500 dark:text-gray-400">3</td>
                                <td class="sticky left-0 z-[5] bg-white px-3 py-3 text-sm text-gray-950 dark:bg-gray-800 dark:text-white">Saldo Akhir</td>
                                @for ($i = 0; $i < 12; $i++)
                                    <td class="px-3 py-3 text-right text-sm tabular-nums text-gray-950 dark:text-white min-w-25 {{ $monthBg[$i] }}" x-text="format(saldoAkhir[{{ $i }}])">{{ number_format($saldoAkhir[$i], 0, ',', '.') }}</td>
                                @endfor
                                <td class="px-3 py-3 text-right text-sm font-semibold tabular-nums text-gray-950 dark:text-white" x-text="format(totals.akhir)">{{ number_format($totalSaldoAkhir, 0, ',', '.') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Legend kecil supaya user paham makna warna & interaksi tanpa nebak-nebak --}}
                <div class="flex flex-wrap items-center gap-4 border-t border-gray-100 px-6 py-3 text-xs text-gray-500 dark:border-white/5 dark:text-gray-400">
                    <span class="inline-flex items-center gap-1.5">
                        <span class="h-2.5 w-2.5 rounded-sm ring-1 ring-inset ring-primary-500 bg-primary-50 dark:bg-primary-500/10"></span>
                        Sedang diedit
                    </span>
                    @if ($this->canInsert || $this->canUpdate)
                        <span class="inline-flex items-center gap-1.5">
                            <x-filament::icon icon="heroicon-o-cursor-arrow-ripple" class="h-3.5 w-3.5" />
                            Double-click sel Penambahan untuk mengedit
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 text-gray-400 dark:text-gray-500">
                            <x-filament::icon icon="heroicon-o-lock-closed" class="h-3.5 w-3.5" />
                            Kolom Penambahan terkunci untuk data ini
                        </span>
                    @endif
                </div>
            @endif
        </div>

        @if ($dataLoaded)
            @php($ringkasan = $this->getRingkasan())
            <div class="mt-6 rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-800 dark:ring-white/10">
                <div class="border-b border-gray-200 px-6 py-4 dark:border-white/10">
                    <h3 class="text-base font-semibold text-gray-950 dark:text-white">Ringkasan Setelah Update</h3>
                </div>

                <div class="grid grid-cols-1 gap-4 p-6 sm:grid-cols-3">
                    <div class="flex items-start gap-3 rounded-lg bg-green-50 p-4 transition-colors dark:bg-green-500/10">
                        <x-filament::icon
                            icon="heroicon-o-document-check"
                            class="h-8 w-8 shrink-0 text-green-600 dark:text-green-400"
                        />
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Total Saldo Akhir Baru</p>
                            <p class="text-lg font-semibold tabular-nums text-gray-950 dark:text-white" x-text="format(totals.akhir)">
                                {{ number_format($ringkasan['saldo_akhir_baru'], 0, ',', '.') }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 rounded-lg bg-gray-50 p-4 transition-colors dark:bg-white/5">
                        <x-filament::icon
                            icon="heroicon-o-arrow-trending-up"
                            class="h-8 w-8 shrink-0 text-gray-500 dark:text-gray-400"
                        />
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Perubahan Total</p>
                            <p class="text-lg font-semibold tabular-nums text-gray-950 dark:text-white" x-text="format(totals.add)">
                                {{ number_format($ringkasan['perubahan_total'], 0, ',', '.') }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 rounded-lg bg-gray-50 p-4 transition-colors dark:bg-white/5">
                        <x-filament::icon
                            icon="heroicon-o-banknotes"
                            class="h-8 w-8 shrink-0 text-gray-500 dark:text-gray-400"
                        />
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Total Saldo Awal</p>
                            <p class="text-lg font-semibold tabular-nums text-gray-950 dark:text-white" x-text="format(totals.awal)">
                                {{ number_format($ringkasan['saldo_awal'], 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Indikator perubahan belum tersimpan, muncul hanya saat dirty --}}
                <div x-show="dirty && !busy" x-transition x-cloak class="border-t border-amber-100 bg-amber-50 px-6 py-3 dark:border-amber-500/10 dark:bg-amber-500/10">
                    <p class="inline-flex items-center gap-1.5 text-sm text-amber-700 dark:text-amber-400">
                        <x-filament::icon icon="heroicon-o-exclamation-triangle" class="h-4 w-4" />
                        Ada perubahan yang belum disimpan. Klik "Update" untuk menyimpan.
                    </p>
                </div>
            </div>
        @endif

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
                x-bind:disabled="!canUpdate || !dirty || busy"
                x-on:click="submitUpdate()"
                class="justify-center"
            >
                <span x-show="!busy">Update</span>
                <span x-show="busy" x-cloak>Updating...</span>
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
