<x-filament-panels::page>
    {{-- Panel Filter Proses Gaji --}}
    <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
        <div class="p-6">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
               {{-- 1. Tanggal Proses Gaji --}}
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Tanggal Proses Gaji <span class="text-red-500">*</span>
                    </label>
                    <div
                        x-data="{
                            init() {
                                flatpickr(this.$refs.dateInput, {
                                    dateFormat: 'Y-m-d',
                                    altInput: true,
                                    altFormat: 'd/m/Y',
                                    defaultDate: @js($tanggalProsesGaji),
                                    onChange: (selectedDates, dateStr) => {
                                        $wire.tanggalProsesGaji = dateStr;
                                    },
                                });
                            }
                        }"
                    >
                        <x-filament::input.wrapper>
                            <x-filament::input
                                type="text"
                                x-ref="dateInput"
                                placeholder="dd/mm/yyyy"
                                class="h-10"
                            />
                        </x-filament::input.wrapper>
                    </div>
                </div>

                {{-- 2, 3, 4. No. Bukti Gaji + Cari + Pilih --}}
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        No. Bukti Gaji
                    </label>
                    <div class="flex items-stretch gap-2">
                        <x-filament::input.wrapper class="flex-1">
                            <x-filament::input
                                type="text"
                                placeholder="BG/MM/YYYY/xxxx"
                                wire:model.live="noBuktiGaji"
                                class="h-10"
                            />
                        </x-filament::input.wrapper>
 
                        {{-- Tombol Cari --}}
                        <button
                            type="button"
                            wire:click="cariNoBukti"
                            title="Cari No. Bukti"
                            class="flex h-10 w-14 shrink-0 items-center justify-center rounded-lg border border-gray-300 text-gray-500 transition hover:bg-gray-50 hover:text-primary-600 dark:border-white/10 dark:text-gray-400 dark:hover:bg-white/5"
                        >
                            <x-filament::icon icon="heroicon-o-magnifying-glass" class="h-4 w-4" />
                        </button>
 
                        {{-- Tombol Pilih --}}
                        <button
                            type="button"
                            wire:click="pilihNoBukti"
                            title="Pilih No. Bukti"
                            class="flex h-10 w-14 shrink-0 items-center justify-center rounded-lg border border-gray-300 text-gray-500 transition hover:bg-gray-50 hover:text-primary-600 dark:border-white/10 dark:text-gray-400 dark:hover:bg-white/5"
                        >
                            <x-filament::icon icon="heroicon-o-ellipsis-horizontal" class="h-4 w-4" />
                        </button>
                    </div>
                </div>

                {{-- 5. Bank/Kas --}}
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Bank / Kas
                    </label>
                    <x-filament::input.wrapper>
                        <x-filament::input.select wire:model.live="bankKas" class="h-10">
                            <option value="">-- Pilih Bank/Kas --</option>
                            @foreach ($bankKasOptions as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                </div> 

                {{-- 6. Lokasi --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Lokasi
                    </label>
                    <x-filament::input.wrapper>
                        <x-filament::input.select wire:model.live="lokasi">
                            <option value="">-- Pilih Lokasi --</option>
                            @foreach ($lokasiOptions as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                </div>
            </div>

            {{-- 7. Show Rekap per Unit Organisasi/Eselon --}}
           <div class="mt-4 flex items-center justify-between gap-4 rounded-lg border border-gray-300 bg-gray-100 px-4 py-3 dark:border-white/10 dark:bg-white/5">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                    View Rekap Gaji per Unit Organisasi / Eselon
                </span>
                <x-filament::button
                    color="primary"
                    wire:click="showRekapPerUnit"
                    class="shrink-0"
                >
                    Show
                </x-filament::button>
            </div>

    {{-- 8, 9, 10. Tabel Rekap Cost Center --}}
    <div class="mt-6 rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
        <div class="flex flex-col gap-1 border-b border-gray-200 px-6 py-4 dark:border-white/10 sm:flex-row sm:items-center sm:justify-between sm:gap-4">
            <h3 class="text-base font-semibold text-gray-950 dark:text-white">
                Daftar Rekap Gaji Cost Center
            </h3>
            <span class="text-sm text-gray-500 dark:text-gray-400">Dalam Rupiah</span>
        </div>

        <div class="max-h-[420px] overflow-x-auto overflow-y-auto">
            <table class="w-full min-w-[860px]">
                <thead class="sticky top-0 z-10 bg-gray-100 dark:bg-white/10">
                    <tr class="border-b border-gray-200 dark:border-white/10">
                        <th class="w-14 px-3 py-3.5 text-center text-sm font-semibold text-gray-950 dark:text-white">No</th>
                        <th class="w-14 px-3 py-3.5 text-center text-sm font-semibold text-gray-950 dark:text-white">Ri</th>
                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-950 dark:text-white">Kode dan Nama Cost Center</th>
                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-950 dark:text-white">Lok.</th>
                        <th class="px-3 py-3.5 text-right text-sm font-semibold text-gray-950 dark:text-white">Besar Gaji</th>
                        <th class="px-3 py-3.5 text-right text-sm font-semibold text-gray-950 dark:text-white">Pihak Lain</th>
                        <th class="px-3 py-3.5 text-right text-sm font-semibold text-gray-950 dark:text-white">Yang Bersangkutan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-white/5">
                    @forelse ($rekapCostCenter as $index => $row)
                        <tr class="even:bg-gray-50 dark:even:bg-white/5">
                            <td class="px-3 py-3 text-center text-sm text-gray-500 dark:text-gray-400">{{ $index + 1 }}</td>
                            <td class="px-3 py-3 text-center">
                                <x-filament::icon-button
                                    icon="heroicon-o-ellipsis-horizontal"
                                    label="Rincian"
                                    tooltip="Lihat Rincian"
                                    wire:click="lihatDaftarKaryawan({{ $index }})"
                                />
                            </td>
                            <td class="px-3 py-3 text-sm text-gray-950 dark:text-white">{{ $row['cost_center'] ?? '-' }}</td>
                            <td class="px-3 py-3 text-sm text-gray-950 dark:text-white">{{ $row['lokasi'] ?? '-' }}</td>
                            <td class="px-3 py-3 text-right text-sm tabular-nums text-gray-950 dark:text-white">{{ number_format($row['besar_gaji'] ?? 0, 0, ',', '.') }}</td>
                            <td class="px-3 py-3 text-right text-sm tabular-nums text-gray-950 dark:text-white">{{ number_format($row['pihak_lain'] ?? 0, 0, ',', '.') }}</td>
                            <td class="px-3 py-3 text-right text-sm tabular-nums text-gray-950 dark:text-white">{{ number_format($row['yang_bersangkutan'] ?? 0, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-3 py-10">
                                <x-filament::empty-state
                                    icon="heroicon-o-document-text"
                                    heading="Belum Ada Data"
                                    description="Pilih Tanggal Proses Gaji dan No. Bukti Gaji, lalu muat data untuk menampilkan rekap Cost Center."
                                />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if (count($rekapCostCenter) > 0)
                    <tfoot>
                            <tr class="border-t border-gray-200 bg-gray-200 dark:border-white/10 dark:bg-white/20">
                            <td colspan="4" class="px-3 py-3 text-right text-sm font-semibold text-gray-950 dark:text-white">TOTAL</td>
                            <td class="px-3 py-3 text-right text-sm font-semibold tabular-nums text-gray-950 dark:text-white">
                                {{ number_format(collect($rekapCostCenter)->sum('besar_gaji'), 0, ',', '.') }}
                            </td>
                            <td class="px-3 py-3 text-right text-sm font-semibold tabular-nums text-gray-950 dark:text-white">
                                {{ number_format(collect($rekapCostCenter)->sum('pihak_lain'), 0, ',', '.') }}
                            </td>
                            <td class="px-3 py-3 text-right text-sm font-semibold tabular-nums text-gray-950 dark:text-white">
                                {{ number_format(collect($rekapCostCenter)->sum('yang_bersangkutan'), 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>

    {{-- 11, 12. Jumlah Gaji Non-Corporate & Entry Rekap Gaji Manual --}}
    <div class="mt-6 grid grid-cols-1 gap-3 sm:flex sm:flex-wrap sm:items-center sm:justify-between sm:gap-4">
        <x-filament::button
            color="info"
            icon="heroicon-m-building-office-2"
            wire:click="jumlahGajiNonCorporate"
            class="justify-center"
        >
            Jumlah Gaji Non Corporate
        </x-filament::button>

        <x-filament::button
            color="success"
            icon="heroicon-m-pencil-square"
            wire:click="entryRekapGajiManual"
            class="justify-center"
        >
            Entry Rekap Gaji (Manual)
        </x-filament::button>
    </div>

    {{-- 13-18. Tombol Aksi --}}
    <div class="mt-6 grid grid-cols-2 gap-3 sm:flex sm:flex-wrap">
        <x-filament::button
            color="primary"
            icon="heroicon-m-printer"
            wire:click="print"
            class="justify-center"
        >
            Print
        </x-filament::button>

        <x-filament::button
            color="success"
            icon="heroicon-m-plus"
            wire:click="insert"
            class="justify-center"
        >
            Insert
        </x-filament::button>

        <x-filament::button
            color="warning"
            icon="heroicon-m-pencil-square"
            wire:click="update"
            class="justify-center"
        >
            Update
        </x-filament::button>

        <x-filament::button
            color="danger"
            icon="heroicon-m-trash"
            wire:click="delete"
            class="justify-center"
        >
            Delete
        </x-filament::button>

        <x-filament::button
            color="gray"
            icon="heroicon-m-x-circle"
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
    {{-- Modal: Rekap per Unit Organisasi / Eselon --}}
    <x-filament::modal id="rekap-per-unit" width="4xl">
        <x-slot name="heading">Rekap Gaji per Unit Organisasi / Eselon</x-slot>

        @include('filament.modals.collecting-gaji.rekap-per-unit')
    </x-filament::modal>

    {{-- Modal: Daftar Gaji Karyawan (Rincian) --}}
    <x-filament::modal id="daftar-gaji-karyawan" width="5xl">
        <x-slot name="heading">Daftar Gaji Karyawan (Rincian)</x-slot>

        @include('filament.modals.collecting-gaji.daftar-gaji-karyawan')
    </x-filament::modal>

    {{-- Modal: Jumlah Gaji Non Corporate --}}
    <x-filament::modal id="gaji-non-corporate" width="4xl">
        <x-slot name="heading">Jumlah Gaji Non Corporate</x-slot>

        @include('filament.modals.collecting-gaji.gaji-non-corporate')
    </x-filament::modal>
     @once
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.js" defer></script>
    @endonce
</x-filament-panels::page>