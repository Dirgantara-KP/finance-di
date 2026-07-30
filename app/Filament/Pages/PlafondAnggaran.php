<?php

namespace App\Filament\Pages;

use App\Models\TmbdgtPlafond;
use App\Models\Tmcontr;
use App\Models\Trchartacct;
use App\Models\Vororg;
use App\Models\Vpon;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use UnitEnum;

class PlafondAnggaran extends Page
{
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationLabel = 'Update Plafond Anggaran';

    protected static ?string $title = 'Update Plafond Anggaran';

    protected static ?string $slug = 'plafond-anggaran';

    protected string $view = 'filament.pages.plafond-anggaran';

    protected static string | UnitEnum | null $navigationGroup = 'Anggaran';

    public $tahun;

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
        $this->tahun = $currentYear;

        $this->organisasiOptions = Vororg::whereNotNull('C_ORG_CUR')
            ->where('C_ORG_ASSETSTAT', 'OPN')
            ->orderBy('C_ORG_CUR')
            ->get()
            ->mapWithKeys(fn ($item) => [
                $item->C_ORG_CUR => $item->C_ORG_CUR . ' || ' . $item->N_ORG_CUR,
            ])
            ->toArray();

        $this->sandiOptions = Trchartacct::where('C_COST_BSIS', 'CC')
            ->where('C_COST_ACCTSUB', '1')
            ->get()
            ->mapWithKeys(fn ($item) => [
                $item->C_COST => $item->C_COST . ' || ' . $item->E_COST,
            ])
            ->toArray();

        $this->ponOptions = Vpon::where('C_PGM_VERACT', 'OPN')
            ->get()
            ->mapWithKeys(fn ($item) => [
                $item->C_PGM_VER => $item->C_PGM_VER . ' || ' . $item->E_PGM,
            ])
            ->toArray();

        $this->resetMonthData();
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
            $this->kontrakOptions = Tmcontr::where('C_ORG_CONTR', $this->organisasi)
                ->pluck('I_CONTR', 'I_CONTR')
                ->toArray();
        }
    }

    public function updatedTahun(): void
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
            'tahun' => 'required',
            'organisasi' => 'required',
            'sandi' => 'required',
            'pon' => 'required',
            'kontrak' => 'required',
        ];
    }

    public function loadData(): void
    {
        $this->validate();

        $ponRecord = Vpon::where('C_PGM_VER', $this->pon)->first();
        if ($ponRecord) {
            $this->cPgm = $ponRecord->C_PGM;
            $this->cPgmSub = $ponRecord->C_PGM_SUB;
            $this->namaProgram = $ponRecord->E_PGM;
        }

        $sandiRecord = Trchartacct::where('C_COST', $this->sandi)->first();
        $this->namaSandi = $sandiRecord->E_COST ?? '';

        $kontrakRecord = Tmcontr::where('I_CONTR', $this->kontrak)
            ->where('C_ORG_CONTR', $this->organisasi)
            ->first();
        $this->cOrgContr = $kontrakRecord->C_ORG_CONTR ?? $this->organisasi;

        $record = TmbdgtPlafond::where('C_BDGT_ANGGARAN', $this->tahun)
            ->where('C_ORG', 'LIKE', $this->organisasi . '%')
            ->where('C_PGM_VER', $this->pon)
            ->where('C_COA_DR', $this->sandi)
            ->where(DB::raw("C_ORG_CONTR || '-' || I_CONTR"), $this->cOrgContr . '-' . $this->kontrak)
            ->first();

        $this->resetMonthData();
        $this->dataLoaded = true;

        if ($record) {
            $this->existingId = $record->id;

            for ($i = 1; $i <= 12; $i++) {
                $saldoField = "V_BDGT_SALDOMONTH{$i}";
                $addField = "V_BDGT_ADDMONTH{$i}";
                $this->saldoAwal[$i - 1] = (int) ($record->$saldoField ?? 0);
                $this->addMonth[$i - 1] = (int) ($record->$addField ?? 0);
            }

            $this->calculateAll();

            $currentYear = (int) date('Y');
            if ((int) $this->tahun === $currentYear && $record->C_BDGT_CONTRSTAT === 'A3') {
                $this->canUpdate = true;
            }
        } else {
            $currentYear = (int) date('Y');
            if ((int) $this->tahun === $currentYear) {
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
            $this->saldoAwal[$i] = (int) ($this->saldoAwal[$i] ?? 0);
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

    public function updated($property): void
    {
        if (str_starts_with((string) $property, 'addMonth')) {
            $this->calculateAll();
        }
    }

    public function insert(): void
    {
        $this->validate();

        try {
            DB::beginTransaction();

            $data = [
                'C_SOURCE' => 'COL',
                'C_ORG_ID' => 'CO',
                'C_ORG' => $this->organisasi,
                'C_ORG_CONTR' => $this->cOrgContr,
                'I_CONTR' => $this->kontrak,
                'C_BDGT_CONTRSTAT' => 'A3',
                'C_BDGT_CONTRINEX' => 'I',
                'C_BDGT_ANGGARAN' => $this->tahun,
                'C_PGM' => $this->cPgm,
                'C_PGM_SUB' => $this->cPgmSub,
                'C_PGM_VER' => $this->pon,
                'C_COA_DR' => $this->sandi,
                'C_COA_CR' => 'A23',
                'C_CY' => 'IDR',
                'I_ENTRY' => '900293',
                'D_ENTRY' => now(),
                'C_ORG_CENTER' => $this->cOrgContr,
            ];

            for ($i = 1; $i <= 12; $i++) {
                $data["V_BDGT_ADDMONTH{$i}"] = (int) ($this->addMonth[$i - 1] ?? 0);
                $data["V_BDGT_SALDOMONTH{$i}"] = (int) ($this->saldoAkhir[$i - 1] ?? 0);
            }

            $data['V_BDGT_ADDTOTAL'] = $this->totalPenambahan;
            $data['V_BDGT_PLANTOTAL'] = $this->totalPenambahan;
            $data['V_BDGT_SALDOTOTAL'] = $this->totalSaldoAkhir;

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
                $updateData["V_BDGT_ADDMONTH{$i}"] = (int) ($this->addMonth[$i - 1] ?? 0);
                $updateData["V_BDGT_SALDOMONTH{$i}"] = (int) ($this->saldoAkhir[$i - 1] ?? 0);
            }
            $updateData['V_BDGT_ADDTOTAL'] = $this->totalPenambahan;
            $updateData['V_BDGT_SALDOTOTAL'] = $this->totalSaldoAkhir;

            $record->update($updateData);

            DB::commit();

            Notification::make()
                ->title('Data Plafond Anggaran berhasil diupdate')
                ->success()
                ->send();

            $this->loadData();
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
        if ($this->existingId) {
            $this->loadData();
        } else {
            $this->resetMonthData();
        }
    }

    public function close(): void
    {
        $this->redirect('/admin');
    }
}
