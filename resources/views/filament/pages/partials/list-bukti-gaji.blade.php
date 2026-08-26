<div class="space-y-4">
    <div class="flex items-center justify-between">
        <p class="text-primary-600 font-semibold text-sm">Daftar Bukti Gaji Bulan Mei 2025</p>
        <x-filament::button size="sm" color="gray" icon="heroicon-o-arrow-path" wire:click="refreshBuktiGaji">
            Refresh
        </x-filament::button>
    </div>

    {{-- Filter --}}
    <div style="display:grid; grid-template-columns: max-content 180px 1fr; column-gap:24px; row-gap:6px; align-items:center;">
        <label class="text-xs font-medium text-gray-600 dark:text-gray-300" style="white-space:nowrap;">Periode Tanggal Gaji</label>
        <label class="text-xs font-medium text-gray-600 dark:text-gray-300" style="white-space:nowrap;">Dibayar Via (Bank)</label>
        <label class="text-xs font-medium text-gray-600 dark:text-gray-300" style="white-space:nowrap;">Nama Bank</label>

        <div style="display:flex; align-items:center; gap:8px;">
            <div style="width:150px;">
                <x-filament::input.wrapper>
                    <x-filament::input type="date" wire:model="filterBuktiGaji.tgl_dari" />
                </x-filament::input.wrapper>
            </div>
            <span class="text-xs text-gray-500" style="white-space:nowrap;">s/d</span>
            <div style="width:150px;">
                <x-filament::input.wrapper>
                    <x-filament::input type="date" wire:model="filterBuktiGaji.tgl_sampai" />
                </x-filament::input.wrapper>
            </div>
        </div>

        <x-filament::input.wrapper>
            <x-filament::input.select wire:model="filterBuktiGaji.bank">
                <option value="">- Semua -</option>
                <option value="BCA">BCA</option>
                <option value="BNI">BNI</option>
                <option value="BRI">BRI</option>
                <option value="MANDIRI">MANDIRI</option>
            </x-filament::input.select>
        </x-filament::input.wrapper>

        <div style="display:flex; align-items:center; gap:8px;">
            <div style="flex:1; min-width:0;">
                <x-filament::input.wrapper>
                    <x-filament::input type="text" wire:model="filterBuktiGaji.nama_bank" placeholder="Cari nama bank..." />
                </x-filament::input.wrapper>
            </div>
            <x-filament::button size="sm" icon="heroicon-o-magnifying-glass" wire:click="cariBuktiGaji" style="flex-shrink:0;">
                Cari
            </x-filament::button>
        </div>
    </div>

    {{-- Tabel --}}
    <div class="overflow-x-auto rounded-lg">
        <table class="w-full text-sm" style="border-collapse: collapse;">
            <thead style="background-color: var(--primary-600); color: #ffffff;">
                <tr class="text-left">
                    <th class="py-2 px-3 font-medium">No.</th>
                    <th class="py-2 px-3 font-medium">Tanggal Gaji</th>
                    <th class="py-2 px-3 font-medium">Dibayar Via</th>
                    <th class="py-2 px-3 font-medium text-right">Jumlah (IDR)</th>
                    <th class="py-2 px-3 font-medium">Nama Bank</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($hasil['items'] as $i => $item)
                    @php
                        $isSelected = $selectedBuktiGajiItem
                            && $selectedBuktiGajiItem['tgl_gaji'] === $item['tgl_gaji']
                            && $selectedBuktiGajiItem['bank_kode'] === $item['bank_kode'];
                    @endphp
                    <tr
                        wire:click="pilihBarisBuktiGaji('{{ $item['tgl_gaji'] }}', '{{ $item['bank_kode'] }}', '{{ $item['bank_nama'] }}')"
                        x-on:dblclick="$wire.pilihLangsungBuktiGaji('{{ $item['tgl_gaji'] }}', '{{ $item['bank_kode'] }}', '{{ $item['bank_nama'] }}')"
                        style="border-bottom: 1px solid #e5e7eb;"
                        @class([
                            'cursor-pointer hover:bg-primary-50 dark:hover:bg-primary-900/20',
                            'bg-primary-100 dark:bg-primary-900/40 ring-1 ring-inset ring-primary-500' => $isSelected,
                            'bg-blue-50/50 dark:bg-gray-800/40' => ! $isSelected && $i % 2 === 1,
                        ])
                    >
                        <td class="py-2 px-3">{{ ($hasil['page'] - 1) * $hasil['perPage'] + $i + 1 }}</td>
                        <td class="py-2 px-3">{{ \Carbon\Carbon::parse($item['tgl_gaji'])->format('d-m-Y') }}</td>
                        <td class="py-2 px-3">{{ $item['bank_kode'] }}</td>
                        <td class="py-2 px-3 text-right">{{ number_format($item['jumlah'], 0, ',', '.') }}</td>
                        <td class="py-2 px-3">{{ $item['bank_nama'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-6 text-center text-gray-400">Tidak ada data ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <style>
        .fd-page-btn:hover:not(:disabled) {
            border-color: var(--primary-500) !important;
            color: var(--primary-600) !important;
        }
    </style>

    {{-- Pagination --}}
    <div class="flex items-center justify-between text-sm" style="margin-top: 4px;">
        <p class="text-gray-500">
            Menampilkan {{ count($hasil['items']) ? ($hasil['page'] - 1) * $hasil['perPage'] + 1 : 0 }}
            sampai {{ ($hasil['page'] - 1) * $hasil['perPage'] + count($hasil['items']) }}
            dari {{ $hasil['total'] }} data
        </p>

        <div class="flex items-center gap-1">
            <button type="button" wire:click="gantiHalamanBuktiGaji({{ max($hasil['page'] - 1, 1) }})"
                class="fd-page-btn px-2 py-1 rounded border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 disabled:opacity-40"
                @disabled($hasil['page'] <= 1)>
                &lt;
            </button>

            @for ($p = 1; $p <= $hasil['lastPage']; $p++)
                <button type="button" wire:click="gantiHalamanBuktiGaji({{ $p }})"
                    @if ($p === $hasil['page'])
                        style="background-color: var(--primary-600); color: #ffffff; border-color: var(--primary-600);"
                        class="px-3 py-1 rounded border text-sm font-medium"
                    @else
                        class="fd-page-btn px-3 py-1 rounded border text-sm border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200"
                    @endif
                >
                    {{ $p }}
                </button>
            @endfor

            <button type="button" wire:click="gantiHalamanBuktiGaji({{ min($hasil['page'] + 1, $hasil['lastPage']) }})"
                class="fd-page-btn px-2 py-1 rounded border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 disabled:opacity-40"
                @disabled($hasil['page'] >= $hasil['lastPage'])>
                &gt;
            </button>
        </div>
    </div>

    {{-- Keterangan --}}
    <div class="flex items-start gap-2 rounded-lg p-3 text-sm"
        style="margin-top: 10px; background-color: var(--primary-50); color: var(--primary-700);">
        <x-heroicon-o-information-circle class="w-5 h-5 shrink-0 mt-0.5" style="color: var(--primary-600);" />
        <div>
            <p class="font-semibold" style="color: var(--primary-800);">Keterangan</p>
            <p>Pilih salah satu data dengan double click pada baris atau klik tombol [Pilih] untuk menggunakan data tersebut. Data yang dipilih akan digunakan sebagai Tanggal Proses Gaji dan Bank Pembayaran.</p>
        </div>
    </div>
</div>