<?php

namespace Database\Seeders;

use App\Models\TmbdgtPlafond;
use App\Models\Vpon;
use Illuminate\Database\Seeder;

class TmbdgtPlafondSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/tmbdgtplafond.csv');

        if (! file_exists($path)) {
            $this->command->warn("File tidak ditemukan: {$path}");

            return;
        }

        $handle = fopen($path, 'r');
        $header = fgetcsv($handle, 0, ';');

        // Preload seluruh record Vpon untuk lookup c_pgm, c_pgm_sub, c_pgm_ver
        $vponMap = Vpon::all()->keyBy('c_pgm_ver');

        // Key unique plafond (match migration plafond_uniq_key).
        // upsert: semua baris CSV masuk, konten terselaraskan kalau CSV berubah.
        $keyCols = ['c_bdgt_anggaran', 'c_org', 'c_org_id', 'c_pgm', 'c_pgm_sub', 'c_pgm_ver', 'c_coa_dr', 'i_contr'];

        $rows = [];

        while (($data = fgetcsv($handle, 0, ';')) !== false) {
            $str = fn (int $i): ?string => isset($data[$i]) && trim($data[$i]) !== '' ? trim($data[$i]) : null;
            $num = fn (int $i): int => isset($data[$i]) && trim($data[$i]) !== '' ? (int) trim($data[$i]) : 0;
            $months = fn (int $first, string $key): array => array_combine(
                array_map(fn (int $i) => $key.$i, range(1, 12)),
                array_map($num, range($first, $first + 11)),
            );

            $c_pgm_ver = $str(9);
            $vpon = $c_pgm_ver ? ($vponMap->get($c_pgm_ver)) : null;

            $rows[] = [
                'c_source' => $str(0) ?? 'COL',
                'c_org_id' => $str(1) ?? 'CO',
                'c_org' => $str(2),
                'i_contr' => $str(3),
                'c_bdgt_contrstat' => $str(4) ?? 'A3',
                'c_bdgt_contrinex' => $str(5) ?? 'I',
                'c_bdgt_anggaran' => $str(6) !== null ? (int) $str(6) : null,
                'c_pgm' => $vpon?->c_pgm ?? $str(7),
                'c_pgm_sub' => $vpon?->c_pgm_sub ?? $str(8),
                'c_pgm_ver' => $vpon?->c_pgm_ver ?? $str(9),
                'c_coa_dr' => $str(10),
                'c_coa_cr' => $str(11) ?? 'A23',
                'c_cy' => $str(12) ?? 'IDR',
                ...$months(13, 'v_bdgt_month'),
                'v_bdgt_total' => $num(25),
                ...$months(26, 'v_bdgt_saldomonth'),
                'v_bdgt_saldototal' => $num(38),
                'i_entry' => $str(39),
                'd_entry' => $str(40),
                'c_org_center' => $str(41),
                'c_org_dit' => $str(42),
                'c_bdgt_cat' => $str(43) ?? '-',
                ...$months(44, 'v_bdgt_planmonth'),
                'v_bdgt_plantotal' => $num(56),
                'c_stat_bdgt' => $str(57),
                'i_ref_updbdgt' => $str(58),
                'd_ref_updbdgt' => $str(59),
                'c_org_contr' => $str(60),
                ...$months(61, 'v_bdgt_addmonth'),
                'v_bdgt_addtotal' => $num(73),
                // Status default di DB adalah OPN (OPEN). Hanya di-insert saat row baru (create)
                // karena c_bdgt_stat tidak ada di contentCols() -> status user di UI tetap aman.
                'c_bdgt_stat' => $str(74) ?? 'OPN',
                'i_pmn' => $str(75),
                'd_pmn' => $str(76),
                'i_order' => $str(77),
            ];

            if (count($rows) >= 500) {
                TmbdgtPlafond::query()->upsert($rows, $keyCols, $this->contentCols());
                $rows = [];
            }
        }

        fclose($handle);

        if (! empty($rows)) {
            TmbdgtPlafond::query()->upsert($rows, $keyCols, $this->contentCols());
        }

        $this->command->info('TmbdgtPlafondSeeder selesai: '.TmbdgtPlafond::query()->count().' baris di-alokasikan (upsert).');
    }

    /** @return list<string> Kolom isi yang ikut di-update saat key sudah ada (semua kecuali key unique). */
    private function contentCols(): array
    {
        return [
            'c_source', 'c_org_contr',
            'c_bdgt_contrstat', 'c_bdgt_contrinex', 'c_coa_cr', 'c_cy',
            ...array_map(fn (int $i) => 'v_bdgt_month'.$i, range(1, 12)),
            'v_bdgt_total',
            ...array_map(fn (int $i) => 'v_bdgt_saldomonth'.$i, range(1, 12)),
            'v_bdgt_saldototal',
            'i_entry', 'd_entry', 'c_org_center', 'c_org_dit', 'c_bdgt_cat',
            ...array_map(fn (int $i) => 'v_bdgt_planmonth'.$i, range(1, 12)),
            'v_bdgt_plantotal',
            'c_stat_bdgt', 'i_ref_updbdgt', 'd_ref_updbdgt',
            ...array_map(fn (int $i) => 'v_bdgt_addmonth'.$i, range(1, 12)),
            'v_bdgt_addtotal',
            // c_bdgt_stat TIDAK di-upsert: status OPN/CLS dikelola lewat UI (setStat),
            // CSV semua '' -> kalau ikut ter-update, status CLS user hilang kena seed ulang.
            'i_pmn', 'd_pmn', 'i_order',
        ];
    }
}
