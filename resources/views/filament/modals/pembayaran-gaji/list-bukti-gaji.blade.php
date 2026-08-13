<div
    x-data="{ open: @entangle('isOpen') }"
    x-show="open"
    x-cloak
    x-on:keydown.escape.window="open = false; $wire.close()"
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
>
    <div
        x-show="open"
        x-transition.opacity
        class="absolute inset-0 bg-gray-950/50"
        wire:click="close"
    ></div>

    <div
        x-show="open"
        x-transition.scale.origin.top
        class="relative w-full max-w-4xl max-h-[90vh] flex flex-col rounded-2xl bg-white dark:bg-gray-900 shadow-xl overflow-hidden"
    >
        {{-- Header --}}
        <div class="flex items-center justify-between border-b border-gray-100 dark:border-white/10 px-6 py-4">
            <h2 class="text-lg font-semibold text-gray-950 dark:text-white">List Bukti Gaji</h2>
            <button type="button" wire:click="close" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                <x-heroicon-o-x-mark class="w-5 h-5" />
            </button>
        </div>

        <div class="flex-1 overflow-y-auto px-6 py-4 space-y-4">
            {{-- Sub header --}}
            <div class="flex items-center justify-between">
                <h3 class="font-semibold text-primary-600">Daftar Bukti Gaji</h3>
                <x-filament::button color="gray" size="sm" icon="heroicon-o-arrow-path" wire:click="refreshData">
                    Refresh
                </x-filament::button>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="text-xs font-medium text-gray-500 dark:text-gray-400">Periode Tanggal Gaji</label>
                    <div class="mt-1 flex items-center gap-2">
                        <x-filament::input.wrapper>
                            <x-filament::input type="date" wire:model="tanggalDari" />
                        </x-filament::input.wrapper>
                        <span class="text-sm text-gray-500">s/d</span>
                        <x-filament::input.wrapper>
                            <x-filament::input type="date" wire:model="tanggalSampai" />
                        </x-filament::input.wrapper>
                    </div>
                </div>

                <div>
                    <label class="text-xs font-medium text-gray-500 dark:text-gray-400">Dibayar Via (Bank)</label>
                    <x-filament::input.wrapper class="mt-1">
                        <x-filament::input.select wire:model="bankFilter">
                            <option value="">- Semua -</option>
                            <option value="BCA">BCA</option>
                            <option value="BNI">BNI</option>
                            <option value="BRI">BRI</option>
                            <option value="MANDIRI">MANDIRI</option>
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                </div>

                <div>
                    <label class="text-xs font-medium text-gray-500 dark:text-gray-400">Nama Bank</label>
                    <div class="mt-1 flex items-center gap-2">
                        <x-filament::input.wrapper class="flex-1">
                            <x-filament::input type="text" placeholder="Cari nama bank..." wire:model="namaBankCari" />
                        </x-filament::input.wrapper>
                        <x-filament::button icon="heroicon-o-magnifying-glass" wire:click="cari">
                            Cari
                        </x-filament::button>
                    </div>
                </div>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-white/10">
                <table class="w-full text-sm">
                    <thead class="bg-primary-600 text-white">
                        <tr>
                            <th class="px-3 py-2 text-left">No.</th>
                            <th class="px-3 py-2 text-left">Tanggal Gaji</th>
                            <th class="px-3 py-2 text-left">Dibayar Via</th>
                            <th class="px-3 py-2 text-right">Jumlah (IDR)</th>
                            <th class="px-3 py-2 text-left">Nama Bank</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($this->records as $i => $row)
                            <tr
                                wire:click="pilihBaris({{ $row['id'] }})"
                                wire:dblclick="pilihLangsung({{ $row['id'] }})"
                                @class([
                                    'border-t border-gray-100 dark:border-white/5 cursor-pointer',
                                    'bg-primary-50 dark:bg-primary-500/10' => $selectedId === $row['id'],
                                    'hover:bg-gray-50 dark:hover:bg-white/5' => $selectedId !== $row['id'],
                                ])
                            >
                                <td class="px-3 py-2">{{ $this->records->firstItem() + $i }}</td>
                                <td class="px-3 py-2">{{ \Illuminate\Support\Carbon::parse($row['tanggal_gaji'])->format('d-m-Y') }}</td>
                                <td class="px-3 py-2">{{ $row['bank_kode'] }}</td>
                                <td class="px-3 py-2 text-right">{{ number_format($row['jumlah'], 2, ',', '.') }}</td>
                                <td class="px-3 py-2">{{ $row['bank_nama'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-3 py-6 text-center text-gray-500">
                                    Tidak ada data yang cocok dengan filter.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination footer --}}
            <div class="flex items-center justify-between">
                <p class="text-sm text-gray-500">
                    @if ($this->records->total() > 0)
                        Menampilkan {{ $this->records->firstItem() }} sampai {{ $this->records->lastItem() }}
                        dari {{ $this->records->total() }} data
                    @else
                        Tidak ada data
                    @endif
                </p>

                @if ($this->records->lastPage() > 1)
                    <div class="flex items-center gap-1">
                        <button
                            type="button"
                            wire:click="previousPage"
                            @disabled($this->records->onFirstPage())
                            class="w-8 h-8 grid place-items-center rounded-lg border border-gray-200 dark:border-white/10 disabled:opacity-40"
                        >
                            <x-heroicon-o-chevron-left class="w-4 h-4" />
                        </button>

                        @for ($page = 1; $page <= $this->records->lastPage(); $page++)
                            <button
                                type="button"
                                wire:click="gotoPage({{ $page }})"
                                @class([
                                    'w-8 h-8 grid place-items-center rounded-lg text-sm',
                                    'bg-primary-600 text-white' => $page === $this->records->currentPage(),
                                    'border border-gray-200 dark:border-white/10' => $page !== $this->records->currentPage(),
                                ])
                            >
                                {{ $page }}
                            </button>
                        @endfor

                        <button
                            type="button"
                            wire:click="nextPage"
                            @disabled(! $this->records->hasMorePages())
                            class="w-8 h-8 grid place-items-center rounded-lg border border-gray-200 dark:border-white/10 disabled:opacity-40"
                        >
                            <x-heroicon-o-chevron-right class="w-4 h-4" />
                        </button>
                    </div>
                @endif
            </div>

            {{-- Keterangan --}}
            <div class="flex gap-3 rounded-xl bg-primary-50 dark:bg-primary-500/10 px-4 py-3">
                <x-heroicon-o-information-circle class="w-5 h-5 shrink-0 text-primary-600" />
                <div class="text-sm text-primary-800 dark:text-primary-200">
                    <p class="font-semibold">Keterangan</p>
                    <p>Pilih salah satu data dengan double click pada baris atau klik tombol [Pilih] untuk menggunakan data tersebut.</p>
                    <p>Data yang dipilih akan digunakan sebagai Tanggal Proses Gaji dan Bank Pembayaran.</p>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="flex items-center justify-end gap-3 border-t border-gray-100 dark:border-white/10 px-6 py-4">
            <x-filament::button color="gray" icon="heroicon-o-x-mark" wire:click="close">
                Tutup
            </x-filament::button>
            <x-filament::button icon="heroicon-o-check" wire:click="konfirmasiPilih" :disabled="! $selectedId">
                Pilih
            </x-filament::button>
        </div>
    </div>
</div>
