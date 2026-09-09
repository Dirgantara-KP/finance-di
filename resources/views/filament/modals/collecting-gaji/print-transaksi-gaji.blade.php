{{-- Pilihan Print --}}
<div class="space-y-4">

    <div>
        <h4 class="text-sm font-semibold text-gray-950 dark:text-white">
            Pilihan Print
        </h4>
        <div class="mt-2 rounded-lg border border-gray-200 p-3 dark:border-white/10">
            <div class="space-y-2">

                {{-- Radio, bukan checkbox: FD komponen 1 "Pilih salah satu
                     jenis laporan (Rincian per NIK atau Rekapitulasi)" —
                     harus saling eksklusif, bukan bisa dicentang berbarengan. --}}
                <label class="flex items-center gap-2">
                    <input
                        type="radio"
                        name="jenisPrint"
                        value="rincian"
                        wire:model="jenisPrint"
                        class="border-gray-300 text-primary-600"
                    >

                    <span class="text-sm text-gray-700 dark:text-gray-300">
                        Rincian per NIK
                    </span>
                </label>

                <label class="flex items-center gap-2">
                    <input
                        type="radio"
                        name="jenisPrint"
                        value="rekap"
                        wire:model="jenisPrint"
                        class="border-gray-300 text-primary-600"
                    >

                    <span class="text-sm text-gray-700 dark:text-gray-300">
                        Rekapitulasi
                    </span>
                </label>

            </div>
        </div>
    </div>

    {{-- Instruksi --}}
    <div class="text-sm">
        <strong>Double Click baris yang dimaksud:</strong>
    </div>

    {{-- Otorisator & Originator --}}
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">

        {{-- Otorisator: SELECT i_emp_own1, n_emp_own1, e_pos_own1 FROM
             TMOWNER WHERE c_trans = 'TRS' AND c_org_id = 'CO' --}}
        <div>
            <h4 class="mb-2 text-sm font-semibold">
                Otorisator
            </h4>
            <div class="overflow-hidden rounded-lg border border-gray-200 dark:border-white/10">
                <div class="grid grid-cols-2 bg-gray-100 px-3 py-2 text-xs font-semibold dark:bg-white/10">
                    <span>NIK</span>
                    <span>Nama</span>
                </div>
                <div class="max-h-48 overflow-y-auto">
                    @forelse ($daftarOtorisator as $row)
                        <div
                            wire:dblclick="pilihOtorisator('{{ $row['nik'] }}', '{{ addslashes($row['nama']) }}')"
                            title="Double click untuk memilih"
                            class="grid cursor-pointer grid-cols-2 border-t border-gray-100 px-3 py-2 text-sm hover:bg-primary-50 dark:border-white/5 dark:hover:bg-white/5 {{ $otorisatorNIK === $row['nik'] ? 'bg-primary-50 dark:bg-white/10' : '' }}"
                        >
                            <span>{{ $row['nik'] }}</span>
                            <span>{{ $row['nama'] }}</span>
                        </div>
                    @empty
                        <div class="px-3 py-4 text-center text-sm text-gray-400">
                            Tidak ada data Otorisator.
                        </div>
                    @endforelse
                </div>
            </div>
            <x-filament::input
            type="text"
            readonly
            :value="$otorisatorNIK ? $otorisatorNIK . ' - ' . $otorisatorNama : ''"
            placeholder="NIK - Nama Otorisator terpilih"
            class="mt-2"
        />
        </div>

        {{-- Originator: SELECT i_emp_own2, n_emp_own2 FROM TMOWNER
             WHERE c_trans = 'UPH' AND c_org_id = 'CO' --}}
        <div>
            <h4 class="mb-2 text-sm font-semibold">Originator</h4>
            <div class="overflow-hidden rounded-lg border border-gray-200 dark:border-white/10">
                <div class="grid grid-cols-2 bg-gray-100 px-3 py-2 text-xs font-semibold dark:bg-white/10">
                    <span>NIK</span>
                    <span>Nama</span>
                </div>
                <div class="max-h-48 overflow-y-auto">
                    @forelse ($daftarOriginator as $row)
                        <div
                            wire:dblclick="pilihOriginator('{{ $row['nik'] }}', '{{ addslashes($row['nama']) }}')"
                            title="Double click untuk memilih"
                            class="grid cursor-pointer grid-cols-2 border-t border-gray-100 px-3 py-2 text-sm hover:bg-primary-50 dark:border-white/5 dark:hover:bg-white/5 {{ $originatorNIK === $row['nik'] ? 'bg-primary-50 dark:bg-white/10' : '' }}"
                        >
                            <span>{{ $row['nik'] }}</span>
                            <span>{{ $row['nama'] }}</span>
                        </div>
                    @empty
                        <div class="px-3 py-4 text-center text-sm text-gray-400">
                            Tidak ada data Originator.
                        </div>
                    @endforelse
                </div>
            </div>

                <x-filament::input
                type="text"
                readonly
                :value="$originatorNIK ? $originatorNIK . ' - ' . $originatorNama : ''"
                placeholder="NIK - Nama Originator terpilih"
                class="mt-2"
            />
        </div>
    </div>

    {{-- Tombol --}}
    <div class="mt-5 flex justify-end gap-2">

        <x-filament::button
            color="primary"
            wire:click="konfirmasiCetak"
        >
            OK
        </x-filament::button>

        <x-filament::button
            color="gray"
            x-on:click="$dispatch('close-modal', { id: 'print-transaksi-gaji' })"
        >
            Cancel
        </x-filament::button>

    </div>

</div>