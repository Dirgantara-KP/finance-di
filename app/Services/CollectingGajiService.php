<?php

namespace App\Services;

use App\Models\VempSalPay;
use App\Repositories\TmemSalPayRepository;
use App\Repositories\VempSalPayEmpRepository;
use App\Repositories\VempSalPayRepository;
use Illuminate\Database\Eloquent\Collection;

final class CollectingGajiService
{
    public function __construct(
        private readonly VempSalPayRepository $repo,
        private readonly TmemSalPayRepository $tmemSalPayRepo,
        private readonly VempSalPayEmpRepository $vempSalPayEmpRepo,
    ) {}

    /**
     * @return array{noBukti: array<int,string>, bankKas: array<string,string>, lokasi: array<string,string>}
     */
    public function getDropdownOptions(string $tanggalProsesGaji, ?array $orgEselon = null): array
    {
        $rows = $this->repo->findByPeriode($tanggalProsesGaji, $orgEselon);

        return [
            'noBukti' => $rows->pluck('NOMOR_BUKTI_GAJI')->unique()->values()->all(),
            'bankKas' => $rows->pluck('DIBAYAR_VIA')->unique()->values()->mapWithKeys(fn ($v) => [$v => $v])->all(),
            'lokasi' => $rows->pluck('LOKASI')->unique()->values()->mapWithKeys(fn ($v) => [$v => $v])->all(),
        ];
    }

    /**
     * @return Collection<int, VempSalPay>
     */
    public function getDaftarBukti(string $tanggalProsesGaji): Collection
    {
        return $this->repo->findByPeriode($tanggalProsesGaji);
    }

    /**
     * Rekap gaji per Cost Center dari data MENTAH VEMPSALPAYEMP (preview
     * sebelum tombol Insert ditekan). Dipicu oleh tombol SHOW.
     * Lihat: VempSalPayEmpRepository::findRekapCostCenter() — substitute Query 03.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getRekapCostCenter(
        string $tanggalProsesGaji,
        ?string $bankKas = null,
        ?string $lokasi = null,
        ?string $nomorBukti = null,
        ?array $orgEselon = null,
    ): array {
        $rows = $this->tmemSalPayRepo->findRekapCostCenter(
            tanggalProsesGaji: $tanggalProsesGaji,
            bankKas: $bankKas,
            lokasi: $lokasi,
            nomorBukti: $nomorBukti,
            orgEselon: $orgEselon,
        );

        return $rows->map(function ($row) {
            $attrs = $row->getAttributes();

            return [
                'org_cur' => $attrs['kode_unit_organisasi'],
                'cost_center' => $attrs['kode_unit_organisasi'].' - '.$attrs['nama_unit_organisasi'],
                'lokasi' => $attrs['lokasi'],
                'besar_gaji' => (float) $attrs['besar_gaji'],
                'pihak_lain' => (float) $attrs['pihak_lain'],
                'yang_bersangkutan' => (float) $attrs['yang_bersangkutan'],
            ];
        })->values()->all();
    }

    /**
     * Rincian gaji per karyawan ("Ri") untuk satu baris Cost Center pada
     * tabel Rekap Cost Center, lengkap dengan total per unit.
     * Lihat: VempSalPayEmpRepository::findByCostCenter() — Query 04 + Query 05.
     *
     * @return array{rows: array<int, array<string, mixed>>, total_besar_gaji: float, total_pihak_lain: float, total_yang_bersangkutan: float}
     */
    public function getDetailKaryawan(
        string $tanggalProsesGaji,
        string $orgCur,
        ?string $bankKas = null,
        ?string $lokasi = null,
        ?string $nomorBukti = null,
    ): array {
        $rows = $this->vempSalPayEmpRepo->findByCostCenter(
            tanggalProsesGaji: $tanggalProsesGaji,
            orgCur: $orgCur,
            bankKas: $bankKas,
            lokasi: $lokasi,
            nomorBukti: $nomorBukti,
        );

        $mapped = $rows->map(function ($row) {
            $attrs = $row->getAttributes();

            return [
                'nik' => $attrs['nik'],
                'nama' => $attrs['nama'],
                'unit_org' => $attrs['unit_org'],
                'via' => $attrs['via'],
                'lokasi' => $attrs['lokasi'],
                'besar_gaji' => (float) $attrs['besar_gaji'],
                'pihak_lain' => (float) $attrs['pihak_lain'],
                'yang_bersangkutan' => (float) $attrs['yang_bersangkutan'],
            ];
        })->values();

        return [
            'rows' => $mapped->all(),
            'total_besar_gaji' => (float) $mapped->sum('besar_gaji'),
            'total_pihak_lain' => (float) $mapped->sum('pihak_lain'),
            'total_yang_bersangkutan' => (float) $mapped->sum('yang_bersangkutan'),
        ];
    }
}
