<div
    wire:key="daftar-bukti-gaji-{{ md5(json_encode($daftarBuktiGaji)) }}"
    x-data="{
        search: '',
        bank: '',
        lokasi: '',
        rows: @js($daftarBuktiGaji),

        get filtered() {
            return this.rows.filter(row => {
                const matchSearch = this.search === '' || row.nomor_bukti.toLowerCase().includes(this.search.toLowerCase());
                const matchBank = this.bank === '' || row.dibayar_via === this.bank;
                const matchLokasi = this.lokasi === '' || row.lokasi === this.lokasi;

                return matchSearch && matchBank && matchLokasi;
            });
        },

        get bankOptions() {
            return [...new Set(this.rows.map(r => r.dibayar_via))].sort();
        },

        get lokasiOptions() {
            return [...new Set(this.rows.map(r => r.lokasi))].sort();
        },
    }"
>
    <div class="mb-4 flex items-start gap-2 rounded-lg bg-blue-50 px-4 py-3 text-sm text-blue-700 dark:bg-blue-500/10 dark:text-blue-300">
        <x-filament::icon icon="heroicon-o-information-circle" class="mt-0.5 h-4 w-4 shrink-0" />
        <span>Pilih baris yang akan diproses.</span>
    </div>

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
        <div class="flex-1">
            <x-filament::input.wrapper>
                <x-filament::input type="text" placeholder="Cari nomor bukti gaji" x-model.debounce.300ms="search" />
            </x-filament::input.wrapper>
        </div>

        <div class="w-full sm:w-40">
            <x-filament::input.wrapper>
                <x-filament::input.select x-model="bank">
                    <option value="">Semua</option>
                    <template x-for="option in bankOptions" :key="option">
                        <option :value="option" x-text="option"></option>
                    </template>
                </x-filament::input.select>
            </x-filament::input.wrapper>
        </div>

        <div class="w-full sm:w-40">
            <x-filament::input.wrapper>
                <x-filament::input.select x-model="lokasi">
                    <option value="">Semua</option>
                    <template x-for="option in lokasiOptions" :key="option">
                        <option :value="option" x-text="option"></option>
                    </template>
                </x-filament::input.select>
            </x-filament::input.wrapper>
        </div>
    </div>

    <div class="mt-4 max-h-[400px] overflow-y-auto rounded-lg border border-gray-200 dark:border-white/10">
        <table class="w-full">
            <thead class="sticky top-0 bg-gray-100 dark:bg-white/10">
                <tr class="border-b border-gray-200 dark:border-white/10">
                    <th class="px-3 py-3 text-left text-sm font-semibold text-gray-950 dark:text-white">Nomor Bukti Gaji</th>
                    <th class="px-3 py-3 text-left text-sm font-semibold text-gray-950 dark:text-white">Dibayar Via</th>
                    <th class="px-3 py-3 text-left text-sm font-semibold text-gray-950 dark:text-white">Lokasi</th>
                    <th class="px-3 py-3 text-right text-sm font-semibold text-gray-950 dark:text-white">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-200 dark:divide-white/5">
                <template x-for="row in filtered" :key="row.nomor_bukti + row.dibayar_via + row.lokasi">
                    <tr class="even:bg-gray-50 dark:even:bg-white/5">
                        <td class="px-3 py-3 text-sm font-medium text-gray-950 dark:text-white" x-text="row.nomor_bukti"></td>
                        <td class="px-3 py-3 text-sm text-gray-700 dark:text-gray-300" x-text="row.dibayar_via"></td>
                        <td class="px-3 py-3 text-sm text-gray-700 dark:text-gray-300" x-text="row.lokasi"></td>

                        <td class="px-3 py-3 text-right">
                            <button
                                type="button"
                                x-on:click="$wire.pilihBaris(row.nomor_bukti, row.dibayar_via, row.lokasi)"
                                class="inline-flex items-center justify-center rounded-lg bg-primary-600 px-3 py-2 text-sm font-medium text-white transition hover:bg-primary-700"
                            >
                                Pilih
                            </button>
                        </td>
                    </tr>
                </template>

                <tr x-show="filtered.length === 0">
                    <td colspan="4" class="px-3 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                        Tidak ada data yang cocok.
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="mt-3 flex items-center justify-between">
        <span class="text-sm text-gray-500 dark:text-gray-400" x-text="filtered.length + ' data ditemukan'"></span>

        <x-filament::button color="gray" x-on:click="$dispatch('close-modal', { id: 'daftar-bukti-gaji' })">
            Tutup
        </x-filament::button>
    </div>
</div>