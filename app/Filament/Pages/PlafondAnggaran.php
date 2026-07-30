<?php

namespace App\Filament\Pages;

use App\Models\TmbdgtPlafond;
use App\Models\Tmcontr;
use App\Models\Trchartacct;
use App\Models\Vororg;
use App\Models\Vpon;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Illuminate\Support\Facades\DB;

class PlafondAnggaran extends Page implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationLabel = 'Plafond Anggaran';

    protected static ?string $title = 'Plafond Anggaran';

    protected static ?string $slug = 'plafond-anggaran';

    protected string $view = 'filament.pages.plafond-anggaran';

    public $tahunAnggaran;

    public $organisasi;

    public $sandi;

    public $pon;

    public $kontrak;

    public $tahunOptions = [];

    public $organisasiOptions = [];

    public $sandiOptions = [];

    public $ponOptions = [];

    public $kontrakOptions = [];

    public $dataLoaded = false;

    public $canInsert = false;

    public $canUpdate = false;

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

    public function mount(): void
    {
        $currentYear = (int) date('Y');
        $this->tahunOptions = array_combine(
            range($currentYear, $currentYear - 5),
            range($currentYear, $currentYear - 5)
        );
        $this->tahunAnggaran = $currentYear;

        $this->organisasiOptions = Vororg::whereNotNull('c_org_cur')
            ->where('c_org_assetstat', 'OPN')
            ->orderBy('c_org_cur')
            ->get()
            ->mapWithKeys(fn ($item) => [
                $item->c_org_cur => $item->c_org_cur.' || '.$item->n_org_cur,
            ])
            ->toArray();

        $this->sandiOptions = Trchartacct::where('c_cost_bsis', 'CC')
            ->where('c_cost_acctsub', '1')
            ->get()
            ->mapWithKeys(fn ($item) => [
                $item->c_cost => $item->c_cost.' || '.$item->e_cost,
            ])
            ->toArray();

        $this->ponOptions = Vpon::where('c_pgm_veract', 'OPN')
            ->get()
            ->mapWithKeys(fn ($item) => [
                $item->c_pgm_ver => $item->c_pgm_ver.' || '.$item->e_pgm,
            ])
            ->toArray();

        $this->resetMonthData();
    }

    public function getAllFiltersSelectedProperty(): bool
    {
        return $this->tahunAnggaran && $this->organisasi && $this->sandi && $this->pon && $this->kontrak;
    }

    public function resetMonthData(): void
    {
        $this->saldoAwal = array_fill(0, 12, 0);
        $this->addMonth = array_fill(0, 12, 0);
        $this->saldoAkhir = array_fill(0, 12, 0);
        $this->totalSaldoAwal = 0;
        $this->totalPenambahan = 0;
        $this->totalSaldoAkhir = 0;
        $this->dataLoaded = false;
        $this->canInsert = false;
        $this->canUpdate = false;
        $this->existingId = null;
        $this->namaProgram = '';
        $this->namaSandi = '';
    }

    public function updatedOrganisasi(): void
    {
        $this->kontrak = null;
        $this->resetMonthData();
        $this->loadKontrakOptions();
    }

    public function loadKontrakOptions(): void
    {
        $this->kontrakOptions = [];
        if ($this->organisasi) {
            $this->kontrakOptions = Tmcontr::where('c_org_contr', $this->organisasi)
                ->pluck('i_contr', 'i_contr')
                ->toArray();
        }
    }

    public function updatedTahunAnggaran(): void
    {
        $this->resetMonthData();
    }

    public function updatedSandi(): void
    {
        $this->resetMonthData();
    }

    public function updatedPon(): void
    {
        $this->resetMonthData();
    }

    public function updatedKontrak(): void
    {
        $this->resetMonthData();
    }

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

    public function muatData(): void
    {
        $this->validate();

        $ponRecord = Vpon::where('c_pgm_ver', $this->pon)->first();
        if ($ponRecord) {
            $this->cPgm = $ponRecord->c_pgm;
            $this->cPgmSub = $ponRecord->c_pgm_sub;
            $this->namaProgram = $ponRecord->e_pgm;
        }

        $sandiRecord = Trchartacct::where('c_cost', $this->sandi)->first();
        $this->namaSandi = $sandiRecord->e_cost ?? '';

        $kontrakRecord = Tmcontr::where('i_contr', $this->kontrak)
            ->where('c_org_contr', $this->organisasi)
            ->first();
        $this->cOrgContr = $kontrakRecord->c_org_contr ?? $this->organisasi;

        $record = TmbdgtPlafond::where('c_bdgt_anggaran', $this->tahunAnggaran)
            ->where('c_org', 'LIKE', $this->organisasi.'%')
            ->where('c_pgm_ver', $this->pon)
            ->where('c_coa_dr', $this->sandi)
            ->whereRaw("CONCAT(c_org_contr, '-', i_contr) = ?", [$this->cOrgContr.'-'.$this->kontrak])
            ->first();

        $this->resetMonthData();
        $this->dataLoaded = true;

        if ($record) {
            $this->existingId = $record->id;

            for ($i = 1; $i <= 12; $i++) {
                $saldoField = "v_bdgt_saldomonth{$i}";
                $addField = "v_bdgt_addmonth{$i}";
                $this->saldoAwal[$i - 1] = (int) ($record->$saldoField ?? 0);
                $this->addMonth[$i - 1] = (int) ($record->$addField ?? 0);
            }

            $this->calculateAll();

            $currentYear = (int) date('Y');
            if ((int) $this->tahunAnggaran === $currentYear && $record->c_bdgt_contrstat === 'A3') {
                $this->canUpdate = true;
            }
        } else {
            $currentYear = (int) date('Y');
            if ((int) $this->tahunAnggaran === $currentYear) {
                $this->canInsert = true;
            }
        }
    }

    public function calculateAll(): void
    {
        $totalAwal = 0;
        $totalAdd = 0;
        $totalAkhir = 0;
        for ($i = 0; $i < 12; $i++) {
            $this->addMonth[$i] = (int) ($this->addMonth[$i] ?? 0);
            $this->saldoAkhir[$i] = $this->saldoAwal[$i] + $this->addMonth[$i];
            $totalAwal += $this->saldoAwal[$i];
            $totalAdd += $this->addMonth[$i];
            $totalAkhir += $this->saldoAkhir[$i];
        }
        $this->totalSaldoAwal = $totalAwal;
        $this->totalPenambahan = $totalAdd;
        $this->totalSaldoAkhir = $totalAkhir;
    }

    public function restoreState(array $state): void
    {
        $this->tahunAnggaran = $state['filters']['tahunAnggaran'] ?? $this->tahunAnggaran;
        $this->organisasi = $state['filters']['organisasi'] ?? $this->organisasi;
        $this->sandi = $state['filters']['sandi'] ?? $this->sandi;
        $this->pon = $state['filters']['pon'] ?? $this->pon;
        $this->kontrak = $state['filters']['kontrak'] ?? $this->kontrak;
        $this->loadKontrakOptions();

        if (! empty($state['dataLoaded'])) {
            $this->dataLoaded = true;
            $this->existingId = $state['existingId'] ?? null;
            $this->canUpdate = $state['canUpdate'] ?? false;
            $this->canInsert = $state['canInsert'] ?? false;
            $this->saldoAwal = $state['saldoAwal'] ?? array_fill(0, 12, 0);
            $this->addMonth = $state['addMonth'] ?? array_fill(0, 12, 0);
            $this->calculateAll();
        }
    }

    public function getRingkasan(): array
    {
        return [
            'saldo_akhir_baru' => $this->totalSaldoAkhir,
            'perubahan_total' => $this->totalPenambahan,
            'saldo_awal' => $this->totalSaldoAwal,
        ];
    }

    public function updated($property): void
    {
        if (str_starts_with((string) $property, 'addMonth')) {
            $this->calculateAll();
        }
    }

    public function insert(): void
    {
        if (! $this->canInsert) {
            return;
        }

        $this->mountAction('insert');
    }

    public function insertAction(): Action
    {
        return Action::make('insert')
            ->label('Insert')
            ->modalHeading('Tambah Data Plafond Anggaran')
            ->modalDescription(fn () => "Tahun {$this->tahunAnggaran} | Org {$this->organisasi} | Sandi {$this->sandi} | PON {$this->pon} | Kontrak {$this->kontrak}")
            ->modalSubmitActionLabel('Simpan')
            ->modalCancelActionLabel('Batal')
            ->modalIcon('heroicon-o-plus-circle')
            ->schema([
                Grid::make(4)
                    ->schema([
                        TextInput::make('bulan_1')->label('Jan')->numeric()->default(0)->minValue(0)->required(),
                        TextInput::make('bulan_2')->label('Feb')->numeric()->default(0)->minValue(0)->required(),
                        TextInput::make('bulan_3')->label('Mar')->numeric()->default(0)->minValue(0)->required(),
                        TextInput::make('bulan_4')->label('Apr')->numeric()->default(0)->minValue(0)->required(),
                        TextInput::make('bulan_5')->label('Mei')->numeric()->default(0)->minValue(0)->required(),
                        TextInput::make('bulan_6')->label('Jun')->numeric()->default(0)->minValue(0)->required(),
                        TextInput::make('bulan_7')->label('Jul')->numeric()->default(0)->minValue(0)->required(),
                        TextInput::make('bulan_8')->label('Agt')->numeric()->default(0)->minValue(0)->required(),
                        TextInput::make('bulan_9')->label('Sep')->numeric()->default(0)->minValue(0)->required(),
                        TextInput::make('bulan_10')->label('Okt')->numeric()->default(0)->minValue(0)->required(),
                        TextInput::make('bulan_11')->label('Nov')->numeric()->default(0)->minValue(0)->required(),
                        TextInput::make('bulan_12')->label('Des')->numeric()->default(0)->minValue(0)->required(),
                    ]),
            ])
            ->action(function (array $data): void {
                $this->performInsert($data);
            });
    }

    private function performInsert(array $formData): void
    {
        $this->validate();

        try {
            DB::beginTransaction();

            $addTotal = 0;
            $monthly = [];

            for ($i = 1; $i <= 12; $i++) {
                $val = (int) ($formData["bulan_{$i}"] ?? 0);
                $monthly[$i] = $val;
                $addTotal += $val;
            }

            $data = [
                'c_source' => 'COL',
                'c_org_id' => 'CO',
                'c_org' => $this->organisasi,
                'c_org_contr' => $this->cOrgContr,
                'i_contr' => $this->kontrak,
                'c_bdgt_contrstat' => 'A3',
                'c_bdgt_contrinex' => 'I',
                'c_bdgt_anggaran' => $this->tahunAnggaran,
                'c_pgm' => $this->cPgm,
                'c_pgm_sub' => $this->cPgmSub,
                'c_pgm_ver' => $this->pon,
                'c_coa_dr' => $this->sandi,
                'c_coa_cr' => 'A23',
                'c_cy' => 'IDR',
                'i_entry' => '900293',
                'd_entry' => now(),
                'c_org_center' => $this->cOrgContr,
            ];

            foreach ($monthly as $i => $val) {
                $data["v_bdgt_addmonth{$i}"] = $val;
                $data["v_bdgt_saldomonth{$i}"] = $val;
            }

            $data['v_bdgt_addtotal'] = $addTotal;
            $data['v_bdgt_plantotal'] = $addTotal;
            $data['v_bdgt_saldototal'] = $addTotal;

            TmbdgtPlafond::create($data);

            DB::commit();

            Notification::make()
                ->title('Data Plafond Anggaran berhasil disimpan')
                ->success()
                ->send();

            $this->resetMonthData();
        } catch (\Exception $e) {
            DB::rollBack();
            Notification::make()
                ->title('Gagal menyimpan data')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function update(): void
    {
        $this->validate();

        try {
            DB::beginTransaction();

            $record = TmbdgtPlafond::find($this->existingId);
            if (! $record) {
                throw new \Exception('Data tidak ditemukan');
            }

            $updateData = [];
            for ($i = 1; $i <= 12; $i++) {
                $updateData["v_bdgt_addmonth{$i}"] = (int) ($this->addMonth[$i - 1] ?? 0);
                $updateData["v_bdgt_saldomonth{$i}"] = (int) ($this->saldoAkhir[$i - 1] ?? 0);
            }
            $updateData['v_bdgt_addtotal'] = $this->totalPenambahan;
            $updateData['v_bdgt_saldototal'] = $this->totalSaldoAkhir;

            $record->update($updateData);

            DB::commit();

            Notification::make()
                ->title('Data Plafond Anggaran berhasil diupdate')
                ->success()
                ->send();

            $this->muatData();
        } catch (\Exception $e) {
            DB::rollBack();
            Notification::make()
                ->title('Gagal mengupdate data')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function cancel(): void
    {
        $this->dispatch('clear-plafond-storage');
        $this->resetMonthData();
    }

    public function autoSavePenambangan(): void
    {
        if (! $this->canUpdate || ! $this->existingId) {
            return;
        }

        try {
            DB::beginTransaction();

            $record = TmbdgtPlafond::find($this->existingId);
            if (! $record) {
                throw new \Exception('Data tidak ditemukan');
            }

            $updateData = [];
            for ($i = 1; $i <= 12; $i++) {
                $updateData["v_bdgt_addmonth{$i}"] = (int) ($this->addMonth[$i - 1] ?? 0);
                $updateData["v_bdgt_saldomonth{$i}"] = (int) ($this->saldoAkhir[$i - 1] ?? 0);
            }
            $updateData['v_bdgt_addtotal'] = $this->totalPenambahan;
            $updateData['v_bdgt_saldototal'] = $this->totalSaldoAkhir;

            $record->update($updateData);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }
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

    public function close(): void
    {
        $this->redirect('/admin');
    }
}
