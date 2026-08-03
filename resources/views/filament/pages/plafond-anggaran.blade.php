<x-filament-panels::page>
    {{-- Panel Filter Plafond Anggaran --}}
    <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-800 dark:ring-white/10">
        <div class="p-6">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
                {{-- 1. Tahun Anggaran --}}
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

                {{-- 2. Organisasi --}}
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

                {{-- 3. Sandi --}}
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

                {{-- 4. PON --}}
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

                {{-- 5. No. Kontrak --}}
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
                    wire:click="muatData"
                    icon="heroicon-m-magnifying-glass"
                    :disabled="! $this->allFiltersSelected"
                >
                    Muat Data
                </x-filament::button>
            </div>
        </div>
    </div>

    {{-- Tabel Rincian Plafond Anggaran --}}
    @php
        $q1 = 'bg-blue-50/50 dark:bg-blue-900/10';
        $q2 = 'bg-emerald-50/50 dark:bg-emerald-900/10';
        $q3 = 'bg-amber-50/50 dark:bg-amber-900/10';
        $q4 = 'bg-violet-50/50 dark:bg-violet-900/10';
        $monthBg = [$q1, $q1, $q1, $q2, $q2, $q2, $q3, $q3, $q3, $q4, $q4, $q4];
        $months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agt','Sep','Okt','Nov','Des'];
    @endphp
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
                <tbody wire:key="plafond-tbody-{{ $dataLoaded ? 'loaded' : 'empty' }}"
                       x-data="{
                            addMonth: {{ json_encode(array_map('intval', $addMonth)) }},
                            saldoAwal: {{ json_encode(array_map('intval', $saldoAwal)) }},
                            saldoAkhir: {{ json_encode(array_map('intval', $saldoAkhir)) }},
                            totals: {
                                awal: {{ $totalSaldoAwal }},
                                add: {{ $totalPenambahan }},
                                akhir: {{ $totalSaldoAkhir }}
                            },
<<<<<<< HEAD
                            init() {
                                const saved = sessionStorage.getItem('plafond_state');
                                const hasPhpData = {{ $dataLoaded ? 'true' : 'false' }};
                                if (hasPhpData) {
                                    this.recalculate();
                                    this.save();
                                } else if (saved) {
                                    try {
                                        const d = JSON.parse(saved);
                                        this.addMonth = d.addMonth || this.addMonth;
                                        this.saldoAwal = d.saldoAwal || this.saldoAwal;
                                        this.saldoAkhir = d.saldoAkhir || this.saldoAkhir;
                                        this.totals = d.totals || this.totals;
                                        if (d.dataLoaded) {
                                            $wire.restoreState(d);
                                        }
                                    } catch (e) {
                                        this.recalculate();
                                    }
                                } else {
                                    this.recalculate();
                                }
                            },
                            save() {
                                sessionStorage.setItem('plafond_state', JSON.stringify({
                                    addMonth: this.addMonth,
                                    saldoAwal: this.saldoAwal,
                                    saldoAkhir: this.saldoAkhir,
                                    totals: this.totals,
                                    filters: {
                                        tahunAnggaran: {{ json_encode($tahunAnggaran) }},
                                        organisasi: {{ json_encode($organisasi) }},
                                        sandi: {{ json_encode($sandi) }},
                                        pon: {{ json_encode($pon) }},
                                        kontrak: {{ json_encode($kontrak) }},
                                    },
                                    dataLoaded: {{ $dataLoaded ? 'true' : 'false' }},
                                    existingId: {{ json_encode($existingId) }},
                                    canUpdate: {{ $canUpdate ? 'true' : 'false' }},
                                }));
                            },
                            clearStorage() {
                                sessionStorage.removeItem('plafond_state');
                                this.addMonth = Array(12).fill(0);
                                this.saldoAwal = Array(12).fill(0);
                                this.saldoAkhir = Array(12).fill(0);
                                this.totals = { awal: 0, add: 0, akhir: 0 };
                            },
                            recalculate() {
                                let tAwal = 0, tAdd = 0, tAkhir = 0;
                                for (let i = 0; i < 12; i++) {
                                    const addVal = parseInt(this.addMonth[i]) || 0;
                                    this.saldoAkhir[i] = this.saldoAwal[i] + addVal;
                                    tAwal += this.saldoAwal[i];
                                    tAdd += addVal;
                                    tAkhir += this.saldoAkhir[i];
                                }
                                this.totals.awal = tAwal;
                                this.totals.add = tAdd;
                                this.totals.akhir = tAkhir;
                                this.save();
                            },
                            format(n) {
                                return Number(n).toLocaleString('id-ID', { maximumFractionDigits: 0 });
                            },
                            isNumKey(e) {
                                 const allowed = ['Backspace','Delete','Tab','Enter','Escape',
                                                  'ArrowLeft','ArrowRight','ArrowUp','ArrowDown','Home','End'];
                                 if (allowed.includes(e.key)) return true;
                                 if ((e.ctrlKey || e.metaKey) && ['a','c','v','x','z'].includes(e.key)) return true;
                                 return e.key >= '0' && e.key <= '9';
                             },
                             _autoTimer: null,
                             autoSave() {
                                 clearTimeout(this._autoTimer);
                                 this._autoTimer = setTimeout(() => {
                                     $wire.autoSavePenambangan();
                                 }, 500);
                             },
                             sync(i) {
                                  const val = parseInt(this.addMonth[i]) || 0;
                                  this.addMonth[i] = val;
                                  this.recalculate();
                                  $wire.set('addMonth.' + i, val);
                                  this.autoSave();
                             }
                         }"
                         @clear-plafond-storage.window="clearStorage()">
=======
                             sync(i) {
                                 const val = parseInt(this.addMonth[i]) || 0;
                                 this.addMonth[i] = val;
                                 this.recalculate();
                                 $wire.set('addMonth.' + i, val);
                            }
                        }">
>>>>>>> b7813e6d48cc21c01997cbb0cf54b890b42644aa
                    {{-- Baris 1: Saldo Awal --}}
                    <tr class="even:bg-gray-50 dark:even:bg-white/5">
                        <td class="px-3 py-3 text-center text-sm text-gray-500 dark:text-gray-400">1</td>
                        <td class="px-3 py-3 text-sm text-gray-950 dark:text-white">Saldo Awal</td>
                        @foreach ($saldoAwal as $i => $val)
                            <td class="px-3 py-3 text-right text-sm tabular-nums text-gray-950 dark:text-white min-w-25 {{ $monthBg[$i] }}">{{ number_format($val, 0, ',', '.') }}</td>
                        @endforeach
                        <td class="px-3 py-3 text-right text-sm font-semibold tabular-nums text-gray-950 dark:text-white">{{ number_format($totalSaldoAwal, 0, ',', '.') }}</td>
                    </tr>

                    {{-- Baris 2: Penambahan --}}
                    <tr class="even:bg-gray-50 dark:even:bg-white/5">
                        <td class="px-3 py-3 text-center text-sm text-gray-500 dark:text-gray-400">2</td>
                        <td class="px-3 py-3 text-sm text-gray-950 dark:text-white">Penambahan</td>
                        @for ($i = 0; $i < 12; $i++)
                            <td class="px-3 py-3 text-right text-sm tabular-nums min-w-25 {{ $monthBg[$i] }}
                                        {{ $this->canUpdate ? 'text-gray-950 dark:text-white cursor-default' : 'text-gray-400 dark:text-gray-500 cursor-not-allowed' }}"
                                :class="{ 'border-2!important border-blue-500!important': editing }"
                                x-data="{ editing: false, saved: 0 }"
                                @dblclick="if (!editing && {{ $this->canUpdate ? 'true' : 'false' }}) { saved = addMonth[{{ $i }}]; editing = true; $nextTick(() => { const el = $refs.i{{ $i }}; el.focus(); el.setSelectionRange(el.value.length, el.value.length) }) }">

                                <span x-show="!editing"
                                      x-text="format(addMonth[{{ $i }}])"
                                      class="block select-none pointer-events-none leading-5">
                                </span>

                                <input x-show="editing"
                                       x-ref="i{{ $i }}"
                                       type="text"
                                       :value="saved"
                                       @keydown="if (!isNumKey($event)) $event.preventDefault()"
                                       @keydown.enter.prevent="$el.blur()"
                                       @keydown.escape.prevent="addMonth[{{ $i }}] = saved; recalculate(); editing = false"
                                       @blur="editing = false;
                                              const raw = $el.value.replace(/\D/g, '');
                                              const val = parseInt(raw) || 0;
                                              addMonth[{{ $i }}] = val;
                                              recalculate(); sync({{ $i }})"
                                       class="w-full text-right outline-none border-0 p-0 bg-transparent leading-5"
                                       inputmode="numeric">
                            </td>
                        @endfor
                        <td class="px-3 py-3 text-right text-sm font-semibold tabular-nums text-gray-950 dark:text-white leading-5" x-text="format(totals.add)">{{ number_format($totalPenambahan, 0, ',', '.') }}</td>
                    </tr>

                    {{-- Baris 3: Saldo Akhir --}}
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

    {{-- Ringkasan Setelah Update --}}
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
                   <p class="text-lg font-semibold tabular-nums text-gray-950 dark:text-white">
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


    {{-- Tombol Aksi --}}
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
            :disabled="! $this->canInsert"
            wire:click="insert"
            class="justify-center"
        >
            Insert
        </x-filament::button>

        <x-filament::button
            color="warning"
            icon="heroicon-m-pencil-square"
            :disabled="! $this->canUpdate"
            wire:click="update"
            class="justify-center"
        >
            Update
        </x-filament::button>

        <x-filament::button
            color="gray"
            icon="heroicon-m-arrow-uturn-left"
            @click="sessionStorage.removeItem('plafond_state')"
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
    <x-filament-actions::modals />
</x-filament-panels::page>
