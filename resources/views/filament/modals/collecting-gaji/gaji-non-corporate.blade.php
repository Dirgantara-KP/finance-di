{{-- TODO(backend): isi tabel ini dari query rekap gaji unit non-corporate --}}
<div class="overflow-x-auto">
    <table class="w-full">
        <thead>
            <tr class="border-b border-gray-200 bg-gray-50 dark:border-white/10 dark:bg-white/5">
                <th class="px-3 py-3.5 text-center text-sm font-semibold text-gray-950 dark:text-white">No</th>
                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-950 dark:text-white">Kode & Nama Unit</th>
                <th class="px-3 py-3.5 text-right text-sm font-semibold text-gray-950 dark:text-white">Besar Gaji</th>
                <th class="px-3 py-3.5 text-right text-sm font-semibold text-gray-950 dark:text-white">Pihak Lain</th>
                <th class="px-3 py-3.5 text-right text-sm font-semibold text-gray-950 dark:text-white">Yang Bersangkutan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="5" class="px-3 py-8">
                    <x-filament::empty-state
                        icon="heroicon-o-building-office-2"
                        heading="Belum Ada Data"
                        description="Rekap gaji non-corporate akan tampil setelah data Collecting Gaji dimuat."
                    />
                </td>
            </tr>
        </tbody>
    </table>
</div>