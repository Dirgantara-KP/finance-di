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
                    <x-filament::input.wrapper :disabled="! $this->organisasi">
                        <x-filament::input.select wire:model.live="sandi" :disabled="! $this->organisasi">
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
                    <x-filament::input.wrapper :disabled="! $this->sandi">
                        <x-filament::input.select wire:model.live="pon" :disabled="! $this->sandi">
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
                    <x-filament::input.wrapper :disabled="! $this->pon">
                        <x-filament::input.select wire:model.live="kontrak" :disabled="! $this->pon">
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

    {{-- Rincian Plafond Anggaran (tabel native Filament, sudah dibuat backend) --}}
    <div class="mt-6">
        {{ $this->table }}
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
</x-filament-panels::page>