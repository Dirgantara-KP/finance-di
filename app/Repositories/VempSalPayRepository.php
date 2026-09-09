<?php

namespace App\Repositories;

use App\Models\VempSalPay;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class VempSalPayRepository
{
    /**
     * Dropdown No. Bukti Gaji.
     * Sumber: VEMPSALPAY.
     */
    public function findDistinctNoBukti(
        string $tanggalProsesGaji
    ): array {
        return VempSalPay::query()
            ->whereDate('d_proc_gaji', $tanggalProsesGaji)
            ->whereNotNull('i_jour')
            ->where('i_jour', '<>', '')
            ->distinct()
            ->orderBy('i_jour')
            ->pluck('i_jour')
            ->map(fn ($value) => trim((string) $value))
            ->filter()
            ->values()
            ->all();
    }

    /**
     * Dropdown Bank/Kas.
     * Sumber: VEMPSALPAY.
     */
    public function findDistinctBankGaji(
        string $tanggalProsesGaji
    ): array {
        return VempSalPay::query()
            ->whereDate('d_proc_gaji', $tanggalProsesGaji)
            ->whereNotNull('c_bank_gaji')
            ->where('c_bank_gaji', '<>', '')
            ->distinct()
            ->orderBy('c_bank_gaji')
            ->pluck('c_bank_gaji')
            ->map(fn ($value) => trim((string) $value))
            ->filter()
            ->values()
            ->all();
    }

    /**
     * Dropdown Lokasi.
     * WAJIB c_emp_payloc.
     */
    public function findDistinctLokasi(
        string $tanggalProsesGaji
    ): array {
        return VempSalPay::query()
            ->whereDate('d_proc_gaji', $tanggalProsesGaji)
            ->whereNotNull('c_emp_payloc')
            ->where('c_emp_payloc', '<>', '')
            ->distinct()
            ->orderBy('c_emp_payloc')
            ->pluck('c_emp_payloc')
            ->map(fn ($value) => trim((string) $value))
            ->filter()
            ->values()
            ->all();
    }

    /**
     * Popup Daftar Bukti Gaji.
     *
     * FD:
     * WHERE periode bulan
     * AND SUBSTR(c_org_cur, 1, 2) IN (:organisasi)
     *
     * Filter bank/lokasi bersifat opsional.
     */
    public function findByPeriode(
        string $periode,
        ?array $orgEselon = null,
        ?string $bankKas = null,
        ?string $lokasi = null,
    ): Collection {
        return VempSalPay::query()
            ->selectRaw(
                'DISTINCT
                    i_jour AS NOMOR_BUKTI_GAJI,
                    c_bank_gaji AS DIBAYAR_VIA,
                    c_emp_payloc AS LOKASI'
            )
            ->whereRaw(
                "DATE_FORMAT(d_proc_gaji, '%Y-%m') = ?",
                [$periode]
            )
            ->when(
                ! empty($orgEselon),
                fn ($query) => $query->whereIn(
                    DB::raw('SUBSTRING(c_org_cur, 1, 2)'),
                    $orgEselon
                )
            )
            ->when(
                filled($bankKas),
                fn ($query) => $query->where(
                    'c_bank_gaji',
                    $bankKas
                )
            )
            ->when(
                filled($lokasi),
                fn ($query) => $query->where(
                    'c_emp_payloc',
                    $lokasi
                )
            )
            ->groupBy(
                'i_jour',
                'c_bank_gaji',
                'c_emp_payloc'
            )
            ->orderBy('NOMOR_BUKTI_GAJI')
            ->orderBy('DIBAYAR_VIA')
            ->orderBy('LOKASI')
            ->get();
    }

    /**
     * Preview Rekap Cost Center dari VEMPSALPAY.
     *
     * Dipakai ketika kombinasi filter belum ditemukan
     * di TMEMPSALPAY.
     */
    public function findRekapCostCenter(
        string $tanggalProsesGaji,
        ?string $nomorGaji = null,
        ?string $bankGaji = null,
        ?string $lokasiBayar = null,
    ): Collection {
        return VempSalPay::query()
            ->from('vempsalpay as a')
            ->join(
                'trorg as b',
                'a.c_org_cur',
                '=',
                'b.c_org_cur'
            )
            ->selectRaw(
                'a.c_org_echl AS org_echl'
            )
            ->selectRaw(
                'a.c_org_cur AS org_cur'
            )
            ->selectRaw(
                'a.c_org_cur AS kode_unit_organisasi'
            )
            ->selectRaw(
                'b.n_org AS nama_unit_organisasi'
            )
            ->selectRaw(
                "CONCAT(a.c_org_cur, ' - ', b.n_org) AS cost_center"
            )
            ->selectRaw(
                'a.c_emp_payloc AS lokasi'
            )
            ->selectRaw(
                'SUM(a.v_emp_tunjgaji) AS besar_gaji'
            )
            ->selectRaw(
                'SUM(a.v_emp_potgaji) AS pihak_lain'
            )
            ->selectRaw(
                'SUM(a.v_emp_tunjgaji) - SUM(a.v_emp_potgaji) AS yang_bersangkutan'
            )
            ->selectRaw(
                'a.c_cost AS coa'
            )
            ->whereDate(
                'a.d_proc_gaji',
                $tanggalProsesGaji
            )
            ->when(
                filled($nomorGaji),
                fn ($query) => $query->where(
                    'a.i_jour',
                    'like',
                    $nomorGaji
                )
            )
            ->when(
                filled($bankGaji),
                fn ($query) => $query->where(
                    'a.c_bank_gaji',
                    'like',
                    $bankGaji
                )
            )
            ->when(
                filled($lokasiBayar),
                fn ($query) => $query->where(
                    'a.c_emp_payloc',
                    'like',
                    $lokasiBayar
                )
            )
            ->groupBy(
                'a.c_org_echl',
                'a.c_org_cur',
                'b.n_org',
                'a.c_emp_payloc',
                'a.c_cost'
            )
            ->orderByRaw('1, 2, 4')
            ->get();
    }
}