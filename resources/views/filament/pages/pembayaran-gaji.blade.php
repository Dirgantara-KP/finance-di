<x-filament-panels::page>
    <form wire:submit.prevent="insert">
        {{ $this->form }}
    </form>

    {{-- === Footer action buttons === --}}
    <div class="flex flex-wrap items-center justify-end gap-3 pt-4">
        <x-filament::button color="gray" icon="heroicon-o-printer" wire:click="print">
            Print
        </x-filament::button>

        <x-filament::button color="success" icon="heroicon-o-plus" wire:click="insert">
            Insert
        </x-filament::button>

        <x-filament::button color="primary" icon="heroicon-o-pencil-square" wire:click="update">
            Update
        </x-filament::button>

        <x-filament::button color="danger" icon="heroicon-o-trash" wire:click="delete"
            wire:confirm="Yakin ingin menghapus data pembayaran gaji ini?">
            Delete
        </x-filament::button>

        <x-filament::button color="warning" icon="heroicon-o-arrow-uturn-left" wire:click="cancel">
            Cancel
        </x-filament::button>

        <x-filament::button color="gray" icon="heroicon-o-x-mark" wire:click="close">
            Close
        </x-filament::button>
    </div>

    {{-- Modal: pencarian & pemilihan Bukti Gaji --}}
    <livewire:list-bukti-gaji />
</x-filament-panels::page>
