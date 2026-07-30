<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Filter Bar - Flex Wrap --}}
        <div class="flex flex-wrap gap-3 items-end">
            <div class="w-full sm:w-auto sm:min-w-[150px]">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Tahun Anggaran
                </label>
                <select
                    wire:model.live="tahun"
                    class="block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500"
                >
                    @foreach ($tahunOptions as $val => $label)
                        <option value="{{ $val }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-full sm:w-auto sm:min-w-[250px]">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Organisasi
                </label>
                <select
                    wire:model.live="organisasi"
                    class="block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500"
                >
                    <option value="">-- Pilih Organisasi --</option>
                    @foreach ($organisasiOptions as $val => $label)
                        <option value="{{ $val }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-full sm:w-auto sm:min-w-[250px]">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Sandi
                </label>
                <select
                    wire:model.live="sandi"
                    class="block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500"
                >
                    <option value="">-- Pilih Sandi --</option>
                    @foreach ($sandiOptions as $val => $label)
                        <option value="{{ $val }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-full sm:w-auto sm:min-w-[250px]">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    PON
                </label>
                <select
                    wire:model.live="pon"
                    class="block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500"
                >
                    <option value="">-- Pilih PON --</option>
                    @foreach ($ponOptions as $val => $label)
                        <option value="{{ $val }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-full sm:w-auto sm:min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    No. Kontrak
                </label>
                <select
                    wire:model.live="kontrak"
                    class="block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500"
                >
                    <option value="">-- Pilih Kontrak --</option>
                    @foreach ($kontrakOptions as $val => $label)
                        <option value="{{ $val }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-full sm:w-auto flex gap-2 pt-5">
                <x-filament::button wire:click="loadData" color="primary">
                    Load
                </x-filament::button>
            </div>
        </div>

        {{-- Table --}}
        @if ($dataLoaded)
            <div class="overflow-x-auto rounded-xl shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-800">
                            <th class="px-3 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700 w-1">No</th>
                            <th class="px-3 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700 min-w-[120px]">Uraian</th>
                            @for ($i = 1; $i <= 12; $i++)
                                <th class="px-2 py-3 text-right text-sm font-semibold text-gray-700 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700 min-w-[100px]">
                                    {{ \Carbon\Carbon::create()->month($i)->isoFormat('MMM') }}
                                </th>
                            @endfor
                            <th class="px-3 py-3 text-right text-sm font-semibold text-gray-700 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700 min-w-[110px]">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Row 1: Saldo Awal --}}
                        <tr class="bg-white dark:bg-gray-900">
                            <td class="px-3 py-3 text-sm text-gray-500 border-b border-gray-100 dark:border-gray-800">1</td>
                            <td class="px-3 py-3 text-sm font-medium text-gray-900 dark:text-gray-100 border-b border-gray-100 dark:border-gray-800">Saldo Awal</td>
                            @for ($i = 0; $i < 12; $i++)
                                <td class="px-2 py-3 text-sm text-right text-gray-700 dark:text-gray-300 border-b border-gray-100 dark:border-gray-800">
                                    {{ number_format((int) ($saldoAwal[$i] ?? 0), 0, ',', '.') }}
                                </td>
                            @endfor
                            <td class="px-3 py-3 text-sm text-right font-semibold text-gray-900 dark:text-gray-100 border-b border-gray-100 dark:border-gray-800">
                                {{ number_format($totalSaldoAwal, 0, ',', '.') }}
                            </td>
                        </tr>

                        {{-- Row 2: Penambahan (Editable) --}}
                        <tr class="bg-white dark:bg-gray-900">
                            <td class="px-3 py-3 text-sm text-gray-500 border-b border-gray-100 dark:border-gray-800">2</td>
                            <td class="px-3 py-3 text-sm font-medium text-gray-900 dark:text-gray-100 border-b border-gray-100 dark:border-gray-800">
                                Penambahan
                                @if ($canInsert)
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-success-100 text-success-700 dark:bg-success-500/20 dark:text-success-400 ml-1">Baru</span>
                                @endif
                            </td>
                            @for ($i = 0; $i < 12; $i++)
                                <td class="px-2 py-2 text-sm border-b border-gray-100 dark:border-gray-800">
                                    <input
                                        type="text"
                                        inputmode="numeric"
                                        wire:model.live="addMonth.{{ $i }}"
                                        class="w-full text-right text-sm text-gray-900 dark:text-gray-100 bg-transparent border-0 border-b-2 border-transparent hover:border-gray-300 focus:border-primary-500 focus:ring-0 px-1 py-0.5 transition-colors"
                                    />
                                </td>
                            @endfor
                            <td class="px-3 py-3 text-sm text-right font-semibold text-primary-600 dark:text-primary-400 border-b border-gray-100 dark:border-gray-800">
                                {{ number_format($totalPenambahan, 0, ',', '.') }}
                            </td>
                        </tr>

                        {{-- Row 3: Saldo Akhir --}}
                        <tr class="bg-gray-50/50 dark:bg-gray-800/50">
                            <td class="px-3 py-3 text-sm text-gray-500 dark:text-gray-400">3</td>
                            <td class="px-3 py-3 text-sm font-medium text-gray-900 dark:text-gray-100">Saldo Akhir</td>
                            @for ($i = 0; $i < 12; $i++)
                                <td class="px-2 py-3 text-sm text-right font-medium text-gray-900 dark:text-gray-100">
                                    {{ number_format((int) ($saldoAkhir[$i] ?? 0), 0, ',', '.') }}
                                </td>
                            @endfor
                            <td class="px-3 py-3 text-sm text-right font-bold text-gray-900 dark:text-gray-100">
                                {{ number_format($totalSaldoAkhir, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Action Buttons --}}
            <div class="flex flex-wrap gap-2">
                <x-filament::button
                    wire:click="insert"
                    :disabled="!$canInsert"
                    color="success"
                >
                    Insert
                </x-filament::button>
                <x-filament::button
                    wire:click="update"
                    :disabled="!$canUpdate"
                    color="warning"
                >
                    Update
                </x-filament::button>
                <x-filament::button
                    wire:click="cancel"
                    color="gray"
                >
                    Cancel
                </x-filament::button>
                <x-filament::button
                    wire:click="close"
                    color="gray"
                >
                    Close
                </x-filament::button>
            </div>
        @else
            <div class="flex flex-col items-center justify-center py-16 text-gray-400 dark:text-gray-500">
                <x-filament::icon alias="heroicon-o-funnel" class="w-12 h-12 mb-3" />
                <p class="text-sm">Pilih filter dan klik <strong>Load</strong> untuk menampilkan data</p>
            </div>
        @endif
    </div>
</x-filament-panels::page>
