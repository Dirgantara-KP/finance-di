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

        $this->organisasiOptions = Vororg::whereNotNull('c_org_cur')
            ->where('c_org_assetstat', 'OPN')
            ->orderBy('c_org_cur')
            ->get()
            ->mapWithKeys(fn ($item) => [
                $item->c_org_cur => $item->c_org_cur . ' || ' . $item->n_org_cur,
            ])
            ->toArray();

        $this->sandiOptions = Trchartacct::where('c_cost_bsis', 'CC')
            ->where('c_cost_acctsub', '1')
            ->get()
            ->mapWithKeys(fn ($item) => [
                $item->c_cost => $item->c_cost . ' || ' . $item->e_cost,
            ])
            ->toArray();

        $this->ponOptions = Vpon::where('c_pgm_veract', 'OPN')
            ->get()
            ->mapWithKeys(fn ($item) => [
                $item->c_pgm_ver => $item->c_pgm_ver . ' || ' . $item->e_pgm,
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
            $this->kontrakOptions = Tmcontr::where('c_org_contr', $this->organisasi)
                ->pluck('i_contr', 'i_contr')
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

        $record = TmbdgtPlafond::where('c_bdgt_anggaran', $this->tahun)
            ->where('c_org', 'LIKE', $this->organisasi . '%')
            ->where('c_pgm_ver', $this->pon)
            ->where('c_coa_dr', $this->sandi)
            ->where(DB::raw("c_org_contr || '-' || i_contr"), $this->cOrgContr . '-' . $this->kontrak)
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
            if ((int) $this->tahun === $currentYear && $record->c_bdgt_contrstat === 'A3') {
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
                'c_source' => 'COL',
                'c_org_id' => 'CO',
                'c_org' => $this->organisasi,
                'c_org_contr' => $this->cOrgContr,
                'i_contr' => $this->kontrak,
                'c_bdgt_contrstat' => 'A3',
                'c_bdgt_contrinex' => 'I',
                'c_bdgt_anggaran' => $this->tahun,
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

            for ($i = 1; $i <= 12; $i++) {
                $data["v_bdgt_addmonth{$i}"] = (int) ($this->addMonth[$i - 1] ?? 0);
                $data["v_bdgt_saldomonth{$i}"] = (int) ($this->saldoAkhir[$i - 1] ?? 0);
            }

            $data['v_bdgt_addtotal'] = $this->totalPenambahan;
            $data['v_bdgt_plantotal'] = $this->totalPenambahan;
            $data['v_bdgt_saldototal'] = $this->totalSaldoAkhir;

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
