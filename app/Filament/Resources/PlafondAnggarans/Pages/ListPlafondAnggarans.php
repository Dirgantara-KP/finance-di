<?php

namespace App\Filament\Resources\PlafondAnggarans\Pages;

use App\Filament\Resources\PlafondAnggarans\PlafondAnggaranResource;
use App\Models\TmbdgtPlafond;
use App\Models\TmContr;
use App\Models\TrChartAcct;
use App\Models\VpOn;
use App\Models\VrOrg;
use Filament\Facades\Filament;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ListPlafondAnggarans extends ListRecords
{
    protected static string $resource = PlafondAnggaranResource::class;

    protected string $view = 'filament.pages.plafond-anggarans.list-plafond-anggarans';

    public ?string $tahunAnggaran = null;

    public ?string $organisasi = null;

    public ?string $sandi = null;

    public ?string $pon = null;

    public ?string $kontrak = null;

    public array $tahunOptions = [];

    public array $organisasiOptions = [];

    public array $sandiOptions = [];

    public array $ponOptions = [];

    public array $kontrakOptions = [];

    public bool $dataLoaded = false;

    public bool $allFiltersSelected = false;

    public bool $canInsert = false;

    public bool $canUpdate = false;

    public function mount(): void
    {
        parent::mount();

        $this->tahunOptions = [date('Y')];

        $this->refreshButtonStates();
    }

    public function updatedTahunAnggaran(): void
    {
        $this->resetDependentFields('tahunAnggaran');
        $this->dataLoaded = false;

        if ($this->tahunAnggaran) {
            $this->organisasiOptions = VrOrg::where('C_ORG_ASSETSTAT', 'OPN')
                ->whereNotNull('C_ORG_CUR')
                ->where('C_ORG_CUR', '!=', '')
                ->orderBy('C_ORG_CUR')
                ->pluck('N_ORG_CUR', 'C_ORG_CUR')
                ->toArray();
        }

        $this->refreshButtonStates();
    }

    public function updatedOrganisasi(): void
    {
        $this->resetDependentFields('organisasi');
        $this->dataLoaded = false;

        if ($this->organisasi) {
            $this->sandiOptions = TrChartAcct::where('C_COST_BSIS', 'CC')
                ->where('C_COST_ACCTSUB', '1')
                ->orderBy('C_COST')
                ->get()
                ->mapWithKeys(fn ($item) => [$item->C_COST => "{$item->C_COST} - {$item->E_COST}"])
                ->toArray();

            $this->kontrakOptions = TmContr::where('C_ORG_CONTR', $this->organisasi)
                ->orderBy('I_CONTR')
                ->get()
                ->mapWithKeys(fn ($item) => ["{$item->C_ORG_CONTR}-{$item->I_CONTR}" => "{$item->C_ORG_CONTR}-{$item->I_CONTR}"])
                ->toArray();
        }

        $this->refreshButtonStates();
    }

    public function updatedSandi(): void
    {
        $this->resetDependentFields('sandi');
        $this->dataLoaded = false;

        if ($this->sandi) {
            $this->ponOptions = VpOn::where('C_PGM_VERACT', 'OPN')
                ->orderBy('C_PGM_VER')
                ->get()
                ->mapWithKeys(fn ($item) => [$item->C_PGM_VER => "{$item->C_PGM_VER} - {$item->E_PGM}"])
                ->toArray();
        }

        $this->refreshButtonStates();
    }

    public function updatedPon(): void
    {
        $this->resetDependentFields('pon');
        $this->dataLoaded = false;
        $this->refreshButtonStates();
    }

    public function updatedKontrak(): void
    {
        $this->dataLoaded = false;
        $this->refreshButtonStates();
    }

    private function refreshButtonStates(): void
    {
        $isCurrentYear = $this->tahunAnggaran && (int) $this->tahunAnggaran === (int) date('Y');

        $this->allFiltersSelected = $this->tahunAnggaran
            && $this->organisasi
            && $this->sandi
            && $this->pon
            && $this->kontrak;

        $this->canInsert = $isCurrentYear && $this->organisasi && ! $this->sandi;

        $this->canUpdate = $isCurrentYear;
    }

    public function muatData(): void
    {
        if (! $this->allFiltersSelected) {
            return;
        }

        $this->dataLoaded = true;
    }

    public function insert(): void
    {
        if (! $this->canInsert) {
            return;
        }

        $this->redirect(PlafondAnggaranResource::getUrl('create', [
            'tahunAnggaran' => $this->tahunAnggaran,
            'organisasi' => $this->organisasi,
            'sandi' => $this->sandi,
            'pon' => $this->pon,
            'kontrak' => $this->kontrak,
        ]));
    }

    public function update(): void
    {
        if (! $this->canUpdate || ! $this->dataLoaded) {
            return;
        }

        $record = TmbdgtPlafond::withoutGlobalScopes()
            ->where('C_BDGT_ANGGARAN', $this->tahunAnggaran)
            ->where('C_ORG', 'LIKE', $this->organisasi.'%')
            ->where('C_COA_DR', 'LIKE', $this->sandi.'%')
            ->where('C_PGM_VER', $this->pon)
            ->whereRaw("C_ORG_CONTR || '-' || I_CONTR = ?", [$this->kontrak])
            ->first();

        if (! $record) {
            session()->flash('warning', 'Data tidak ditemukan untuk update.');

            return;
        }

        $this->redirect(PlafondAnggaranResource::getUrl('edit', ['record' => $record->id]));
    }

    public function cancel(): void
    {
        $this->tahunAnggaran = null;
        $this->organisasi = null;
        $this->sandi = null;
        $this->pon = null;
        $this->kontrak = null;

        $this->organisasiOptions = [];
        $this->sandiOptions = [];
        $this->ponOptions = [];
        $this->kontrakOptions = [];

        $this->dataLoaded = false;
        $this->refreshButtonStates();
    }

    public function close(): void
    {
        $this->redirect(Filament::getCurrentOrDefaultPanel()->getUrl());
    }

    public function exportExcel(): void
    {
        if (! $this->dataLoaded) {
            return;
        }
    }

    /**
     * Ringkasan untuk 3 kartu "Ringkasan Setelah Update" (UI-only).
     * Method ini TIDAK menulis query baru — hanya membaca ulang hasil
     * dari getTableQuery() yang sudah ada (ditulis oleh backend) untuk
     * ditampilkan sebagai ringkasan. Tidak mengubah logika bisnis apa pun.
     *
     * @return array{saldo_awal: float, perubahan_total: float, saldo_akhir_baru: float}
     */
    public function getRingkasan(): array
    {
        $default = [
            'saldo_awal' => 0.0,
            'perubahan_total' => 0.0,
            'saldo_akhir_baru' => 0.0,
        ];

        if (! $this->dataLoaded) {
            return $default;
        }

        $rows = $this->getTableQuery()->get()->keyBy('uraian');

        return [
            'saldo_awal' => (float) ($rows->get('Saldo Awal')->total ?? 0),
            'perubahan_total' => (float) ($rows->get('Penambahan')->total ?? 0),
            'saldo_akhir_baru' => (float) ($rows->get('Saldo Akhir')->total ?? 0),
        ];
    }

    protected function getTableQuery(): Builder
    {
        if (! $this->dataLoaded) {
            $zeroCols = implode(', ', array_map(fn ($i) => "0 AS month_{$i}", range(0, 11)));

            return TmbdgtPlafond::withoutGlobalScopes()
                ->from(DB::raw("(
                    SELECT 1 AS id, 'Saldo Awal' AS uraian, {$zeroCols}, 0 AS total
                    UNION ALL
                    SELECT 2 AS id, 'Penambahan' AS uraian, {$zeroCols}, 0 AS total
                    UNION ALL
                    SELECT 3 AS id, 'Saldo Akhir' AS uraian, {$zeroCols}, 0 AS total
                ) as ".(new TmbdgtPlafond)->getTable()));
        }

        $table = (new TmbdgtPlafond)->getTable();

        $saldoAwalCols = [];
        $addCols = [];
        $akhirCols = [];

        for ($i = 1; $i <= 12; $i++) {
            $idx = $i - 1;
            $saldoAwalCols[] = "COALESCE(SUM(V_BDGT_SALDOMONTH{$i}), 0) AS month_{$idx}";
            $addCols[] = "COALESCE(SUM(V_BDGT_ADDMONTH{$i}), 0) AS month_{$idx}";
            $akhirCols[] = "COALESCE(SUM(V_BDGT_SALDOMONTH{$i} + V_BDGT_ADDMONTH{$i}), 0) AS month_{$idx}";
        }

        $saldoAwalSql = implode(', ', $saldoAwalCols);
        $addSql = implode(', ', $addCols);
        $akhirSql = implode(', ', $akhirCols);

        $where = 'WHERE deleted_at IS NULL'
            .' AND C_BDGT_ANGGARAN = ?'
            .' AND C_ORG LIKE ?'
            .' AND C_COA_DR LIKE ?'
            .' AND C_PGM_VER = ?'
            ." AND C_ORG_CONTR || '-' || I_CONTR = ?";

        $sql1 = "SELECT 1 AS id, 'Saldo Awal' AS uraian, {$saldoAwalSql}, COALESCE(SUM(V_BDGT_SALDOTOTAL), 0) AS total FROM {$table} {$where}";
        $sql2 = "SELECT 2 AS id, 'Penambahan' AS uraian, {$addSql}, COALESCE(SUM(V_BDGT_ADDTOTAL), 0) AS total FROM {$table} {$where}";
        $sql3 = "SELECT 3 AS id, 'Saldo Akhir' AS uraian, {$akhirSql}, COALESCE(SUM(V_BDGT_SALDOTOTAL + V_BDGT_ADDTOTAL), 0) AS total FROM {$table} {$where}";

        $fullSql = "({$sql1}) UNION ALL ({$sql2}) UNION ALL ({$sql3})";

        $baseBindings = [
            $this->tahunAnggaran,
            $this->organisasi.'%',
            $this->sandi.'%',
            $this->pon,
            $this->kontrak,
        ];

        return TmbdgtPlafond::withoutGlobalScopes()
            ->from(DB::raw("({$fullSql}) as {$table}"))
            ->addBinding(array_merge($baseBindings, $baseBindings, $baseBindings), 'from');
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    private function resetDependentFields(string $changed): void
    {
        $fields = ['tahunAnggaran', 'organisasi', 'sandi', 'pon', 'kontrak'];
        $reset = false;

        foreach ($fields as $field) {
            if ($reset) {
                $this->$field = null;
            }

            if ($field === $changed) {
                $reset = true;
            }
        }

        match ($changed) {
            'tahunAnggaran' => $this->organisasiOptions = $this->sandiOptions = $this->ponOptions = $this->kontrakOptions = [],
            'organisasi' => $this->sandiOptions = $this->ponOptions = $this->kontrakOptions = [],
            'sandi' => $this->ponOptions = [],
            default => null,
        };
    }
}
