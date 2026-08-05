{{-- TODO(backend): isi tabel ini dari query daftar gaji karyawan (rincian) per cost center --}}

{{-- Info cards: Tanggal Proses, No. Bukti/Jurnal, Bank/Kas, Lokasi Pembayaran --}}
<div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
    <div class="rounded-lg border border-gray-200 px-3 py-2.5 dark:border-white/10">
        <p class="text-xs text-gray-500 dark:text-gray-400">Tanggal Proses</p>
        <p class="text-sm font-semibold text-gray-950 dark:text-white">{{ $tanggalProses ?? '-' }}</p>
    </div>
    <div class="rounded-lg border border-gray-200 px-3 py-2.5 dark:border-white/10">
        <p class="text-xs text-gray-500 dark:text-gray-400">No. Bukti/Jurnal</p>
        <p class="text-sm font-semibold text-gray-950 dark:text-white">{{ $noBukti ?? '-' }}</p>
    </div>
    <div class="rounded-lg border border-gray-200 px-3 py-2.5 dark:border-white/10">
        <p class="text-xs text-gray-500 dark:text-gray-400">Bank/Kas</p>
        <p class="text-sm font-semibold text-gray-950 dark:text-white">{{ $bank ?? '-' }}</p>
    </div>
    <div class="rounded-lg border border-gray-200 px-3 py-2.5 dark:border-white/10">
        <p class="text-xs text-gray-500 dark:text-gray-400">Lokasi Pembayaran</p>
        <p class="text-sm font-semibold text-gray-950 dark:text-white">{{ $lokasiPembayaran ?? '-' }}</p>
    </div>
</div>

{{-- Label + counter --}}
<div class="mt-5 flex items-center justify-between">
    <h3 class="text-sm font-semibold text-gray-950 dark:text-white">Rincian Karyawan</h3>
    <span class="text-xs text-gray-500 dark:text-gray-400">{{ count($karyawanRincian ?? []) }} data ditampilkan</span>
</div>

{{-- Tabel Rincian Karyawan --}}
<div class="mt-2 overflow-hidden rounded-xl border border-gray-200 dark:border-white/10">
    <div class="max-h-[320px] overflow-x-auto overflow-y-auto">
        <table class="w-full min-w-[760px]">
            <thead class="sticky top-0 z-10 bg-gray-50 dark:bg-white/5">
                <tr class="border-b border-gray-200 dark:border-white/10">
                    <th class="w-12 px-3 py-3.5 text-center text-sm font-medium text-gray-500 dark:text-gray-400">No.</th>
                    <th class="px-3 py-3.5 text-left text-sm font-medium text-gray-500 dark:text-gray-400">NIK</th>
                    <th class="px-3 py-3.5 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Nama Karyawan</th>
                    <th class="px-3 py-3.5 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Unit Org</th>
                    <th class="px-3 py-3.5 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Via</th>
                    <th class="px-3 py-3.5 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Lok.</th>
                    <th class="px-3 py-3.5 text-right text-sm font-medium text-gray-500 dark:text-gray-400">Besar Gaji</th>
                    <th class="px-3 py-3.5 text-right text-sm font-medium text-gray-500 dark:text-gray-400">Pihak Lain</th>
                    <th class="px-3 py-3.5 text-right text-sm font-medium text-gray-500 dark:text-gray-400">Yang Bersangkutan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                @forelse ($karyawanRincian ?? [] as $index => $row)
                    <tr class="even:bg-gray-50 dark:even:bg-white/5">
                        <td class="px-3 py-3 text-center text-sm text-gray-500 dark:text-gray-400">{{ $index + 1 }}</td>
                        <td class="px-3 py-3 text-sm text-primary-600 dark:text-primary-400">{{ $row['nik'] ?? '-' }}</td>
                        <td class="px-3 py-3 text-sm font-medium text-gray-950 dark:text-white">{{ $row['nama'] ?? '-' }}</td>
                        <td class="px-3 py-3 text-sm text-gray-950 dark:text-white">{{ $row['unit_org'] ?? '-' }}</td>
                        <td class="px-3 py-3 text-sm text-gray-950 dark:text-white">{{ $row['via'] ?? '-' }}</td>
                        <td class="px-3 py-3 text-sm text-gray-950 dark:text-white">{{ $row['lokasi'] ?? '-' }}</td>
                        <td class="px-3 py-3 text-right text-sm tabular-nums text-gray-950 dark:text-white">{{ number_format($row['besar_gaji'] ?? 0, 0, ',', '.') }}</td>
                        <td class="px-3 py-3 text-right text-sm tabular-nums text-gray-950 dark:text-white">{{ number_format($row['pihak_lain'] ?? 0, 0, ',', '.') }}</td>
                        <td class="px-3 py-3 text-right text-sm tabular-nums text-gray-950 dark:text-white">{{ number_format($row['yang_bersangkutan'] ?? 0, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-3 py-8">
                            <x-filament::empty-state
                                icon="heroicon-o-users"
                                heading="Belum Ada Data"
                                description="Rincian gaji karyawan akan tampil setelah data Collecting Gaji dimuat."
                            />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Catatan kecil di bawah tabel --}}
@if (count($karyawanRincian ?? []))
    <p class="mt-2 text-xs text-gray-400 dark:text-gray-500">
        Nama karyawan ditampilkan "-" apabila NIK tidak ditemukan pada master pegawai TPRRMEMPII.
    </p>
@endif

{{-- Bar total: TOTAL UNIT ORG. --}}
@if (count($karyawanRincian ?? []))
    <div class="mt-4 flex flex-wrap items-center justify-between gap-4 rounded-xl bg-gray-50 px-4 py-3 dark:bg-white/5">
        <span class="text-sm font-semibold text-gray-950 dark:text-white">TOTAL UNIT ORG.</span>
        <div class="flex items-center gap-8">
            <div class="text-right">
                <p class="text-xs text-gray-500 dark:text-gray-400">Besar Gaji</p>
                <p class="text-sm font-semibold tabular-nums text-gray-950 dark:text-white">{{ number_format($totalBesarGaji ?? 0, 0, ',', '.') }}</p>
            </div>
            <div class="text-right">
                <p class="text-xs text-gray-500 dark:text-gray-400">Pihak Lain</p>
                <p class="text-sm font-semibold tabular-nums text-gray-950 dark:text-white">{{ number_format($totalPihakLain ?? 0, 0, ',', '.') }}</p>
            </div>
            <div class="text-right">
                <p class="text-xs text-gray-500 dark:text-gray-400">Yang Bersangkutan</p>
                <p class="text-sm font-semibold tabular-nums text-green-600 dark:text-green-400">{{ number_format($totalYangBersangkutan ?? 0, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>
@endif

{{-- Tombol aksi: Tutup / Kembali ke Rekap Gaji --}}
<div class="mt-5 flex justify-end gap-2">
    <x-filament::button color="gray" x-on:click="close">Tutup</x-filament::button>
    <x-filament::button color="primary" wire:click="kembaliKeRekapGaji">Kembali ke Rekap Gaji</x-filament::button>
</div>