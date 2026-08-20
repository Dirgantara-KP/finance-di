<x-filament-panels::page>
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

        {{-- Breadcrumb --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

        <nav
            aria-label="Breadcrumb"
            class="flex items-center gap-2 text-sm"
        >
            {{-- Home --}}
            <x-filament::icon
                icon="heroicon-o-home"
                class="h-5 w-5 text-gray-400 dark:text-gray-500"
            />

            <span class="text-gray-500 dark:text-gray-400">
                Beranda
            </span>

            <x-filament::icon
                icon="heroicon-m-chevron-right"
                class="h-4 w-4 text-gray-400 dark:text-gray-500"
            />

            <span class="text-gray-500 dark:text-gray-400">
                Cash Out
            </span>

            <x-filament::icon
                icon="heroicon-m-chevron-right"
                class="h-4 w-4 text-gray-400 dark:text-gray-500"
            />

            <span class="font-medium text-gray-950 dark:text-white">
                Pembayaran Gaji
            </span>
        </nav>

    </div>

        

    </div>


    <form wire:submit.prevent="insert" class="mt-6">
        {{ $this->form }}
    </form>

    <div class="mt-6 flex w-full justify-center">
        <div class="flex flex-wrap items-center justify-center gap-3">

            <x-filament::button
                color="gray"
                icon="heroicon-o-printer"
                wire:click="print"
            >
                Print
            </x-filament::button>

            <x-filament::button
                color="success"
                icon="heroicon-o-plus"
                wire:click="insert"
            >
                Insert
            </x-filament::button>

            <x-filament::button
                color="primary"
                icon="heroicon-o-pencil-square"
                wire:click="update"
            >
                Update
            </x-filament::button>

            <x-filament::button
                color="danger"
                icon="heroicon-o-trash"
                wire:click="delete"
                wire:confirm="Yakin ingin menghapus data pembayaran gaji ini?"
            >
                Delete
            </x-filament::button>

            <x-filament::button
                color="warning"
                icon="heroicon-o-arrow-uturn-left"
                wire:click="cancel"
            >
                Cancel
            </x-filament::button>

            <x-filament::button
                color="gray"
                icon="heroicon-o-x-mark"
                wire:click="close"
            >
                Close
            </x-filament::button>

        </div>
    </div>

    <livewire:list-bukti-gaji />

</x-filament-panels::page>