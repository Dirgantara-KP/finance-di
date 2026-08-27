<?php

namespace App\Services;

use App\Repositories\ThempSalRefRepository;
use App\Repositories\TmempSalPayRepository;
use App\Repositories\VempSalPayEmpRepository;
use App\Repositories\VempSalPayRepository;
use Illuminate\Support\Collection;

final class CollectingGajiService
{
    public function __construct(
        private readonly VempSalPayRepository $repo,
        private readonly TmempSalPayRepository $tmemSalPayRepo,
        private readonly VempSalPayEmpRepository $vempSalPayEmpRepo,
        private readonly ThempSalRefRepository $thempSalRefRepo,
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
     * @return Collection<int, object{NOMOR_BUKTI_GAJI: string, DIBAYAR_VIA: string, LOKASI: string}>
     */
    public function getDaftarBukti(string $tanggalProsesGaji): Collection
    {
        return $this->repo->findByPeriode($tanggalProsesGaji);
    }

    /**
     * SHOW — flow FD section C:
     * 1) cek dulu apakah kombinasi filter SUDAH ada di TMEMPSALPAY.
     * 2) jika ADA -> tampilkan rekap dari TMEMPSALPAY (query FD point D),
     *    Insert harus disabled, Update enabled.
     * 3) jika BELUM ADA -> tampilkan preview agregasi dari VEMPSALPAY
     *    (query FD point E), Insert enabled, Update disabled.
     * Rekap Cost Center tetap DISPLAY saja (bukan tabel), tidak diedit user.
     *
     * @param  array<int, string>|null  $orgEselon
     * @return array{sudah_ada: bool, rows: array<int, array<string, mixed>>}
     */
    public function getRekapCostCenter(
        string $tanggalProsesGaji,
        ?string $bankKas = null,
        ?string $lokasi = null,
        ?string $nomorBukti = null,
        ?array $orgEselon = null,
    ): array {
        $sudahAda = $this->tmemSalPayRepo->existsForFilter(
            tanggalProsesGaji: $tanggalProsesGaji,
            bankKas: $bankKas,
            lokasi: $lokasi,
            nomorBukti: $nomorBukti,
        );

        if ($sudahAda) {
            $rows = $this->tmemSalPayRepo->findRekapCostCenter(
                tanggalProsesGaji: $tanggalProsesGaji,
                bankKas: $bankKas,
                lokasi: $lokasi,
                nomorBukti: $nomorBukti,
                orgEselon: $orgEselon,
            );

            $mapped = $rows->map(fn ($row) => [
                'org_cur' => $row->kode_unit_organisasi,
                'cost_center' => $row->kode_unit_organisasi.' - '.$row->nama_unit_organisasi,
                'lokasi' => $row->lokasi,
                'besar_gaji' => (float) $row->besar_gaji,
                'pihak_lain' => (float) $row->pihak_lain,
                'yang_bersangkutan' => (float) $row->yang_bersangkutan,
            ])->values()->all();

            return ['sudah_ada' => true, 'rows' => $mapped];
        }

        $rows = $this->repo->findRekapCostCenter(
            periodeGaji: $tanggalProsesGaji,
            nomorGaji: $nomorBukti,
            orgEselon: $orgEselon,
            bankGaji: $bankKas,
            lokasiBayar: $lokasi,
        );

        $mapped = $rows->map(fn ($row) => [
            'org_cur' => $row->kode_unit_organisasi,
            'cost_center' => $row->kode_unit_organisasi.' - '.$row->nama_unit_organisasi,
            'lokasi' => $row->lokasi,
            'besar_gaji' => (float) $row->v_tot_tunjgaji,
            'pihak_lain' => (float) $row->v_tot_potgaji,
            'yang_bersangkutan' => (float) $row->v_gaji_bersih,
        ])->values()->all();

        return ['sudah_ada' => false, 'rows' => $mapped];
    }

    /**
     * INSERT — query FD item F. Hanya boleh dijalankan ketika SHOW
     * sebelumnya melaporkan `sudah_ada = false` (dicek ulang di sini,
     * bukan cuma dipercaya dari state Livewire, supaya tidak terjadi
     * duplikasi kalau ada race/klik ganda).
     *
     * @param  array<int, string>|null  $orgEselon
     *
     * @throws \RuntimeException jika data untuk filter ini sudah ada
     */
    public function insertCollectingGaji(
        string $tanggalProsesGaji,
        string $nomorBukti,
        string $bankKas,
        string $lokasi,
        string $orgId,
        string $iUser,
        ?array $orgEselon = null,
    ): int {
        $sudahAda = $this->tmemSalPayRepo->existsForFilter(
            tanggalProsesGaji: $tanggalProsesGaji,
            bankKas: $bankKas,
            lokasi: $lokasi,
            nomorBukti: $nomorBukti,
        );

        if ($sudahAda) {
            throw new \RuntimeException(
                'Data untuk periode/no. bukti/bank/lokasi ini sudah pernah di-collecting.'
            );
        }

        return $this->tmemSalPayRepo->insertFromVempSalPay(
            periodeGaji: $tanggalProsesGaji,
            nomorBuktiGaji: $nomorBukti,
            orgEselon: $orgEselon,
            bankGaji: $bankKas,
            lokasiBayar: $lokasi,
            orgId: $orgId,
            iUser: $iUser,
        );
    }

    /**
     * UPDATE — query FD item H. TARGET tabel ini THEMPSALREF, bukan
     * TMEMPSALPAY. Hanya masuk akal dijalankan setelah data sudah
     * ter-collecting (sudah_ada = true dari SHOW).
     *
     * @param  array<int, string>  $orgGaji  daftar kode eselon (:OrgGaji)
     */
    public function updateThempSalRef(
        string $tanggalProsesGaji,
        string $orgCur,
        array $orgGaji,
    ): int {
        return $this->thempSalRefRepo->updateOrgPayrecpt(
            periodeGaji: $tanggalProsesGaji,
            orgCur: $orgCur,
            orgGaji: $orgGaji,
        );
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

        $mapped = $rows->map(fn ($row) => [
            'nik' => $row->nik,
            'nama' => $row->nama,
            'unit_org' => $row->unit_org,
            'via' => $row->via,
            'lokasi' => $row->lokasi,
            'besar_gaji' => (float) $row->besar_gaji,
            'pihak_lain' => (float) $row->pihak_lain,
            'yang_bersangkutan' => (float) $row->yang_bersangkutan,
        ])->values();

        return [
            'rows' => $mapped->all(),
            'total_besar_gaji' => (float) $mapped->sum('besar_gaji'),
            'total_pihak_lain' => (float) $mapped->sum('pihak_lain'),
            'total_yang_bersangkutan' => (float) $mapped->sum('yang_bersangkutan'),
        ];
    }
}