{{-- Pilihan Print --}}
<div class="space-y-4">

    <div>
        <h4 class="text-sm font-semibold text-gray-950 dark:text-white">
            Pilihan Print
        </h4>
        <div class="mt-2 rounded-lg border border-gray-200 p-3 dark:border-white/10">
            <div class="space-y-2">

                <label class="flex items-center gap-2">
                    <input
                        type="checkbox"
                        checked
                        class="rounded border-gray-300 text-primary-600"
                    >

                    <span class="text-sm text-gray-700 dark:text-gray-300">
                        Rincian per NIK
                    </span>
                </label>

                <label class="flex items-center gap-2">
                    <input
                        type="checkbox"
                        class="rounded border-gray-300 text-primary-600"
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

        {{-- Otorisator --}}
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
                    {{-- data nanti di sini --}}
                </div>
            </div>
            <x-filament::input
                type="text"
                readonly
                placeholder="NIK - Nama Otorisator terpilih"
                class="mt-2"
            />
        </div>

        {{-- Originator --}}
        <div>
            <h4 class="mb-2 text-sm font-semibold">Originator</h4>
            <div class="overflow-hidden rounded-lg border border-gray-200 dark:border-white/10">
                <div class="grid grid-cols-2 bg-gray-100 px-3 py-2 text-xs font-semibold dark:bg-white/10">
                    <span>NIK</span>
                    <span>Nama</span>
                </div>
                <div class="max-h-48 overflow-y-auto">
                    {{-- data nanti di sini --}}
                </div>
            </div>

            <x-filament::input
                type="text"
                readonly
                placeholder="NIK - Nama Originator terpilih"
                class="mt-2"
            />
        </div>
    </div>

    {{-- Tombol --}}
    <div class="mt-5 flex justify-end gap-2">

        <x-filament::button
            color="primary"
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