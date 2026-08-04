<?php

namespace App\Filament\Pages;

use App\Exceptions\DuplicateTransactionException;
use App\Exceptions\ForbiddenActionException;
use App\Services\PlafondAnggaranService;
use BackedEnum;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Livewire\Attributes\Url;

class PlafondAnggaran extends Page implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationLabel = 'Plafond Anggaran';

    protected static ?string $title = 'Plafond Anggaran';

    protected static ?string $slug = 'plafond-anggaran';

    protected string $view = 'filament.pages.plafond-anggaran';

    private PlafondAnggaranService $service;

    public function boot(PlafondAnggaranService $service): void
    {
        $this->service = $service;
    }

    #[Url(except: null, keep: true)]
    public $tahunAnggaran;

    #[Url(except: null, keep: true)]
    public $organisasi;

    #[Url(except: null, keep: true)]
    public $sandi;

    #[Url(except: null, keep: true)]
    public $pon;

    #[Url(except: null, keep: true)]
    public $kontrak;

    public $tahunOptions = [];

    public $organisasiOptions = [];

    public $sandiOptions = [];

    public $ponOptions = [];

    public $kontrakOptions = [];

    public $dataLoaded = false;

    public $canUpdate = false;

    public $isUpdating = false;

    public $existingId = null;

    public $saldoAwal = [];

    public $addMonth = [];

    public $saldoAkhir = [];

    public $totalSaldoAwal = 0;

    public $totalPenambahan = 0;

    public $totalSaldoAkhir = 0;

    public $namaProgram = '';

    public $namaSandi = '';

    public $cPgm = null;

    public $cPgmSub = null;

    public $cOrgContr = null;

    /** @var array<int,int> snapshot addMonth saat load — baseline untuk dirty tracking */
    public array $cleanAddMonth = [];

    public function mount(): void
    {
        if (! $this->tahunAnggaran) {
            $this->tahunAnggaran = now()->year;
        }

        $options = $this->service->getDropdownOptions($this->organisasi);
        $this->tahunOptions = $options['tahun'];
        $this->organisasiOptions = $options['organisasi'];
        $this->sandiOptions = $options['sandi'];
        $this->ponOptions = $options['pon'];
        $this->kontrakOptions = $options['kontrak'];

        $this->sanitizeUrlFilters();

        $this->resetMonthData();

        if ($this->getAllFiltersSelectedProperty()) {
            try {
                $this->loadData();
            } catch (\Throwable) {
                $this->resetMonthData();
            }
        }
    }

    public function getAllFiltersSelectedProperty(): bool
    {
        return $this->tahunAnggaran && $this->organisasi && $this->sandi && $this->pon && $this->kontrak;
    }

    /**
     * Filter datang dari URL (#[Url]) — bisa basi (opsi dihapus dari master).
     * Nilai yang tak lagi jadi anggota opsi direset, biar load data tak pakai kunci sampah.
     */
    private function sanitizeUrlFilters(): void
    {
        if ($this->tahunAnggaran !== null && ! in_array((int) $this->tahunAnggaran, $this->tahunOptions, true)) {
            $this->tahunAnggaran = now()->year;
        }

        if ($this->organisasi !== null && ! array_key_exists($this->organisasi, $this->organisasiOptions)) {
            $this->organisasi = null;
        }

        if ($this->sandi !== null && ! array_key_exists($this->sandi, $this->sandiOptions)) {
            $this->sandi = null;
        }

        if ($this->pon !== null && ! array_key_exists($this->pon, $this->ponOptions)) {
            $this->pon = null;
        }

        $this->loadContractOptions();

        if ($this->kontrak !== null && ! array_key_exists($this->kontrak, $this->kontrakOptions)) {
            $this->kontrak = null;
        }
    }

    public function getCanInsertProperty(): bool
    {
        $currentYear = now()->year;

        return (int) $this->tahunAnggaran === $currentYear
            && $this->getAllFiltersSelectedProperty()
            && $this->dataLoaded === true
            && $this->existingId === null;
    }

    public function getIsDirtyProperty(): bool
    {
        for ($i = 0; $i < 12; $i++) {
            if ((int) ($this->addMonth[$i] ?? 0) !== (int) ($this->cleanAddMonth[$i] ?? 0)) {
                return true;
            }
        }

        return false;
    }

    public function resetMonthData(): void
    {
        $this->resetMonthValuesOnly();
        $this->dataLoaded = false;
    }

    private function resetMonthValuesOnly(): void
    {
        $this->saldoAwal = array_fill(0, 12, 0);
        $this->addMonth = array_fill(0, 12, 0);
        $this->saldoAkhir = array_fill(0, 12, 0);
        $this->cleanAddMonth = array_fill(0, 12, 0);
        $this->totalSaldoAwal = 0;
        $this->totalPenambahan = 0;
        $this->totalSaldoAkhir = 0;
        $this->canUpdate = false;
        $this->existingId = null;
        $this->namaProgram = '';
        $this->namaSandi = '';
    }

    private function resetAll(): void
    {
        $this->resetMonthData();
    }

    public function updatedOrganisasi(): void
    {
        $this->kontrak = null;
        $this->resetAll();
        $this->loadContractOptions();
    }

    public function loadContractOptions(): void
    {
        $this->kontrakOptions = $this->organisasi
            ? $this->service->getKontrakOptions($this->organisasi)
            : [];
    }

    public function updatedTahunAnggaran(): void
    {
        $this->resetAll();
    }

    public function updatedSandi(): void
    {
        $this->resetAll();
    }

    public function updatedPon(): void
    {
        $this->resetAll();
    }

    public function updatedKontrak(): void
    {
        $this->resetAll();
    }

    /** @return array<string, string|string[]> */
    public function rules(): array
    {
        return [
            'tahunAnggaran' => 'required',
            'organisasi' => 'required',
            'sandi' => 'required',
            'pon' => 'required',
            'kontrak' => 'required',
        ];
    }

    public function loadData(): void
    {
        $this->validate();

        try {
            $state = $this->service->load([
                'tahun' => $this->tahunAnggaran,
                'org' => $this->organisasi,
                'sandi' => $this->sandi,
                'pon' => $this->pon,
                'kontrak' => $this->kontrak,
            ]);
        } catch (\Throwable) {
            $this->resetMonthData();

            throw new \RuntimeException('Gagal memuat data plafond');
        }

        $this->resetMonthValuesOnly();
        $this->applyLoadedState($state);
    }

    public function calculateAll(): void
    {
        $result = $this->service->calculateAll($this->saldoAwal, $this->addMonth);

        $this->addMonth = $result['addMonth'];
        $this->saldoAkhir = $result['saldoAkhir'];
        $this->totalSaldoAwal = $result['totalSaldoAwal'];
        $this->totalPenambahan = $result['totalPenambahan'];
        $this->totalSaldoAkhir = $result['totalSaldoAkhir'];
    }

    public function getRingkasan(): array
    {
        return [
            'saldo_akhir_baru' => $this->totalSaldoAkhir,
            'perubahan_total' => $this->totalPenambahan,
            'saldo_awal' => $this->totalSaldoAwal,
        ];
    }

    public function insert(): void
    {
        // Insert hanya aktif saat data belum ada (existingId null) + semua filter terisi
        if (! $this->getCanInsertProperty()) {
            return;
        }

        $this->performInsert();
    }

    private function performInsert(): void
    {
        $this->validate();

        try {
            $this->service->insert([
                'tahun' => $this->tahunAnggaran,
                'org' => $this->organisasi,
                'sandi' => $this->sandi,
                'pon' => $this->pon,
                'kontrak' => $this->kontrak,
                'add_month' => $this->addMonth,
            ]);

            $this->loadData();

            Notification::make()
                ->title('Data Plafond Anggaran berhasil disimpan')
                ->success()
                ->send();
        } catch (DuplicateTransactionException) {
            $this->loadData();

            Notification::make()
                ->title('Data sudah ada')
                ->body('Silakan gunakan tombol Update untuk mengubah data.')
                ->info()
                ->send();
        } catch (ForbiddenActionException $e) {
            Notification::make()
                ->title('Aksi ditolak')
                ->body($e->getMessage())
                ->warning()
                ->send();
        } catch (\Throwable $e) {
            report($e);
            Notification::make()
                ->title('Gagal menyimpan data')
                ->body($e->getMessage())
                ->danger()
                ->send();
            throw $e;
        }
    }

    public function update(): void
    {
        if ($this->isUpdating) {
            return;
        }

        $this->validate();

        if (! $this->existingId) {
            $this->loadData();

            return;
        }

        if (! $this->isDirty) {
            Notification::make()
                ->title('Tidak ada perubahan')
                ->body('Edit cell Penambahan terlebih dahulu sebelum klik Update.')
                ->info()
                ->send();

            return;
        }

        $this->isUpdating = true;

        try {
            $this->service->update(
                (int) $this->existingId,
                $this->saldoAwal,
                $this->addMonth,
            );

            Notification::make()
                ->title('Data Plafond Anggaran berhasil diupdate')
                ->success()
                ->send();

            $this->loadData();
        } catch (ForbiddenActionException $e) {
            Notification::make()
                ->title('Aksi ditolak')
                ->body($e->getMessage())
                ->warning()
                ->send();
        } catch (\Throwable $e) {
            report($e);
            Notification::make()
                ->title('Gagal mengupdate data')
                ->body($e->getMessage())
                ->danger()
                ->send();
            throw $e;
        } finally {
            $this->isUpdating = false;
        }
    }

    public function clearFilters(): void
    {
        $this->tahunAnggaran = now()->year; // match mount() default — null disables cascading dropdowns
        $this->organisasi = null;
        $this->sandi = null;
        $this->pon = null;
        $this->kontrak = null;
        $this->kontrakOptions = [];
        $this->cPgm = null;
        $this->cPgmSub = null;
        $this->cOrgContr = null;
        $this->resetAll();
    }

    public function cancel(): void
    {
        $this->clearFilters();
    }

    private function applyLoadedState(array $state): void
    {
        $this->cPgm = $state['cPgm'] ?? $this->cPgm;
        $this->cPgmSub = $state['cPgmSub'] ?? $this->cPgmSub;
        $this->namaProgram = $state['namaProgram'] ?? $this->namaProgram;
        $this->namaSandi = $state['namaSandi'] ?? $this->namaSandi;
        $this->cOrgContr = $state['cOrgContr'] ?? $this->cOrgContr;
        $this->existingId = $state['existingId'];
        $this->saldoAwal = $state['saldoAwal'];
        $this->addMonth = $state['addMonth'];
        $this->cleanAddMonth = $state['addMonth'];
        $this->canUpdate = $state['canUpdate'];

        $this->calculateAll();
        $this->dataLoaded = true;

        $this->dispatch('plafond-data-loaded',
            saldoAwal: $this->saldoAwal,
            addMonth: $this->addMonth,
            existingId: $this->existingId,
            canInsert: $this->getCanInsertProperty(),
            canUpdate: $this->canUpdate,
        );
    }

    public function getTitle(): string
    {
        return 'Plafond Anggaran';
    }

    public function exportExcel(): void
    {
        if (! $this->dataLoaded) {
            return;
        }
    }

    public function close(): void
    {
        $this->redirect('/admin');
    }
}
