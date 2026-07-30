{{-- TODO(backend): isi tabel ini dari query rekap gaji per unit organisasi/eselon --}}
<div class="overflow-x-auto">
    <table class="w-full">
        <thead>
            <tr class="border-b border-gray-200 bg-gray-50 dark:border-white/10 dark:bg-white/5">
                <th class="px-3 py-3.5 text-center text-sm font-semibold text-gray-950 dark:text-white">No</th>
                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-950 dark:text-white">Kode & Nama Unit Organisasi</th>
                <th class="px-3 py-3.5 text-right text-sm font-semibold text-gray-950 dark:text-white">Besar Gaji</th>
                <th class="px-3 py-3.5 text-right text-sm font-semibold text-gray-950 dark:text-white">Pihak Lain</th>
                <th class="px-3 py-3.5 text-right text-sm font-semibold text-gray-950 dark:text-white">Yang Bersangkutan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="5" class="px-3 py-8">
                    <x-filament::empty-state
                        icon="heroicon-o-table-cells"
                        heading="Belum Ada Data"
                        description="Rekap per unit organisasi/eselon akan tampil setelah data Collecting Gaji dimuat."
                    />
                </td>
            </tr>
        </tbody>
    </table>
</div>