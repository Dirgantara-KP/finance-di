<?php

namespace Database\Seeders;

use App\Models\Tmcontr;
use Illuminate\Database\Seeder;

class TmcontrSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['i_id_contr' => 1, 'c_org_contr' => 'KU0000', 'i_contr' => '183/AM3000/03/2013', 'i_contr_ref' => '183/AM3000/03/2013', 'n_contr_proj' => 'PINJAM PAKAI LAHAN GEDUNG DIRGANTARA II'],
            ['i_id_contr' => 2, 'c_org_contr' => 'KU0000', 'i_contr' => '152/AM3000/03/2013-P', 'i_contr_ref' => '152/AM3000/03/2013-P', 'n_contr_proj' => 'PINJAM PAKAI LAHAN GEDUNG DIRGANTARA II'],
            ['i_id_contr' => 3, 'c_org_contr' => 'KU0000', 'i_contr' => '152/AM3000/03/2013', 'i_contr_ref' => '152/AM3000/03/2013', 'n_contr_proj' => 'PINJAM PAKAI LAHAN GEDUNG DIRGANTARA II'],
            ['i_id_contr' => 4, 'c_org_contr' => 'KU0000', 'i_contr' => '191/AM3000/03/2013', 'i_contr_ref' => '191/AM3000/03/2013', 'n_contr_proj' => 'PINJAM PAKAI LAHAN GEDUNG DIRGANTARA I'],
            ['i_id_contr' => 5, 'c_org_contr' => 'AS0000', 'i_contr' => 'P4020', 'i_contr_ref' => 'P4020', 'n_contr_proj' => 'SPARE PART SALES'],
            ['i_id_contr' => 6, 'c_org_contr' => 'KU0000', 'i_contr' => 'SPER/819/037.06/PTD/02/2013', 'i_contr_ref' => 'SPER/819/037.06/PTD/02/2013', 'n_contr_proj' => 'PINJAM PAKAI PENGGUNAAN FASILITAS UNTUK ATM BRI DI AREA POS 3 KP-2'],
            ['i_id_contr' => 7, 'c_org_contr' => 'KU0000', 'i_contr' => '170/AM3000/03/2013', 'i_contr_ref' => '170/AM3000/03/2013', 'n_contr_proj' => 'PENAGIHAN BIAYA PEMAKAIAN TELPON KANTOR KAS BRI DI PKSN'],
            ['i_id_contr' => 8, 'c_org_contr' => 'KU0000', 'i_contr' => '166/AM3000/03/2013', 'i_contr_ref' => '166/AM3000/03/2013', 'n_contr_proj' => 'PENAGIHAN BIAYA PEMAKAIAN,PERAWATAN JARINGAN TELPON FEB 2013'],
            ['i_id_contr' => 9, 'c_org_contr' => 'TD0000', 'i_contr' => 'SOW RD-02-3132012', 'i_contr_ref' => 'SOW DT-1301', 'n_contr_proj' => 'Pembuatan Database Dokumens Flight System'],
            ['i_id_contr' => 10, 'c_org_contr' => 'LM0000', 'i_contr' => 'PO : 21032', 'i_contr_ref' => 'BA : 03/LM4200/IX/2002', 'n_contr_proj' => 'Servo Machine Manesty Repair'],
            ['i_id_contr' => 11, 'c_org_contr' => 'IT0000', 'i_contr' => '1003/496.13/IT0000/7/01', 'i_contr_ref' => '1003/496.13/IT0000/7/01', 'n_contr_proj' => 'APLIKASI KK,KTP,AK SERTA PENGADAAN H/W'],
            ['i_id_contr' => 12, 'c_org_contr' => 'AS0000', 'i_contr' => 'KAI-IAE-001-TEST', 'i_contr_ref' => 'KAI-IAE-001-TEST', 'n_contr_proj' => 'B737 INSPAR WING RIB #2 - KAI PROGRAM'],
            ['i_id_contr' => 13, 'c_org_contr' => 'IT0000', 'i_contr' => '1536/-1.823.508', 'i_contr_ref' => '1536/-1.823.508', 'n_contr_proj' => 'REDESIGN & PEMELIHARAAN INTERNET'],
            ['i_id_contr' => 14, 'c_org_contr' => 'MS0000', 'i_contr' => 'FZ-821506-8982N', 'i_contr_ref' => 'FZ-821506-8982N', 'n_contr_proj' => '#1180 Pressure Bulkhead Assembly B-757'],
            ['i_id_contr' => 15, 'c_org_contr' => 'MS0000', 'i_contr' => 'GARUDA/TB/SPK-2031/2002', 'i_contr_ref' => 'DS/PERJ/DT-3203/99', 'n_contr_proj' => 'Jasa Perawatan Pesawat Terbang'],
            ['i_id_contr' => 16, 'c_org_contr' => 'IT0000', 'i_contr' => '1170/E16000/2002-S5', 'i_contr_ref' => '1170/E16000/2002-S5', 'n_contr_proj' => 'SISTEM  PENGEM. SDM BERBASIS KOPENTENSI'],
            ['i_id_contr' => 17, 'c_org_contr' => 'DT0000', 'i_contr' => '002/ITTP-TC/IR-DI/IX/2001', 'i_contr_ref' => '002/ITTP-TC/IR-DI/IX/2001', 'n_contr_proj' => 'ITTP-TC'],
            ['i_id_contr' => 18, 'c_org_contr' => 'KU0000', 'i_contr' => '004/ACS/IR-DI/IX/2001', 'i_contr_ref' => '004/ACS/IR-DI/IX/2001', 'n_contr_proj' => 'Procurement S Parts'],
            ['i_id_contr' => 19, 'c_org_contr' => 'TD0000', 'i_contr' => '090/KS/IX/2002', 'i_contr_ref' => '090/KS/IX/2002', 'n_contr_proj' => 'FFM ANALYSIS'],
            ['i_id_contr' => 20, 'c_org_contr' => 'IT0000', 'i_contr' => '074/496.13/IT0000/02/2002', 'i_contr_ref' => '074/496.13/IT0000/02/2002', 'n_contr_proj' => 'PAYROLL MAINTENANCE'],
            ['i_id_contr' => 21, 'c_org_contr' => 'IN0000', 'i_contr' => '033/F/02', 'i_contr_ref' => '033/F/02', 'n_contr_proj' => 'NC212 Interior Repair'],
            ['i_id_contr' => 22, 'c_org_contr' => 'IT0000', 'i_contr' => '165/496.13/IT0000/02/2002', 'i_contr_ref' => '165/496.13/IT0000/02/2002', 'n_contr_proj' => 'SIUDIN MAINTENANCE'],
            ['i_id_contr' => 23, 'c_org_contr' => 'IT0000', 'i_contr' => '073/496.13/IT0000/01/2002', 'i_contr_ref' => '073/496.13/IT0000/01/2002', 'n_contr_proj' => 'SIUDIN MAINTENANCE'],
            ['i_id_contr' => 24, 'c_org_contr' => 'IT0000', 'i_contr' => '1285/RBI/2002', 'i_contr_ref' => '1285/RBI/2002', 'n_contr_proj' => 'TA'],
            ['i_id_contr' => 25, 'c_org_contr' => 'IT0000', 'i_contr' => '006/GN/X/2001', 'i_contr_ref' => '006/GN/X/2001', 'n_contr_proj' => 'MAINTENANCE CATIA SYSTEM'],
            ['i_id_contr' => 26, 'c_org_contr' => 'IT0000', 'i_contr' => 'PP-0112-IT045', 'i_contr_ref' => 'PP-0112-IT045', 'n_contr_proj' => 'SIMKU'],
            ['i_id_contr' => 27, 'c_org_contr' => 'IT0000', 'i_contr' => '181/073.555', 'i_contr_ref' => '181/073.555', 'n_contr_proj' => 'SOFTWARE ORACLE'],
            ['i_id_contr' => 28, 'c_org_contr' => 'IT0000', 'i_contr' => '1458/496.13/IT0000/X/2001', 'i_contr_ref' => '1458/496.13/IT0000/X/2001', 'n_contr_proj' => 'SIMPEG (GARANSI)'],
            ['i_id_contr' => 29, 'c_org_contr' => 'IT0000', 'i_contr' => '0075-5/IO/DU/PO-PC/2002', 'i_contr_ref' => '0075-5/IO/DU/PO-PC/2002', 'n_contr_proj' => 'PENGADAAN PC (4 UNIT)'],
            ['i_id_contr' => 30, 'c_org_contr' => 'IT0000', 'i_contr' => '0061-4/IO/DU/2002', 'i_contr_ref' => '0061-4/IO/DU/2002', 'n_contr_proj' => 'PELATIHAN EKSEKUTIF OASE'],
            ['i_id_contr' => 31, 'c_org_contr' => 'IT0000', 'i_contr' => '08/PIMPEO-PKSDM/05/2002', 'i_contr_ref' => '08/PIMPEO-PKSDM/05/2002', 'n_contr_proj' => 'KELAS WEB DESIGN & BSCW'],
            ['i_id_contr' => 32, 'c_org_contr' => 'IT0000', 'i_contr' => '034/496.13/IT0000/2/2002', 'i_contr_ref' => '034/496.13/IT0000/2/2002', 'n_contr_proj' => 'SIUDIN PUPUK KUJANG'],
            ['i_id_contr' => 33, 'c_org_contr' => 'IT0000', 'i_contr' => '036/496.13/IT0000/01/2002', 'i_contr_ref' => '036/496.13/IT0000/01/2002', 'n_contr_proj' => 'SIUDIN VONEX'],
            ['i_id_contr' => 34, 'c_org_contr' => 'FT0000', 'i_contr' => 'GARUDA/VZ/Perj./1001/02', 'i_contr_ref' => 'GARUDA/VZ/Perj./1001/02', 'n_contr_proj' => 'PERBAIKAN SLIDE RAFT & LIFE RAFT'],
            ['i_id_contr' => 35, 'c_org_contr' => 'LM0000', 'i_contr' => '13/SPK/UPT-LAGG/BPPT/IX/2001', 'i_contr_ref' => 'Com.Inv.: CI-0110-38037', 'n_contr_proj' => 'Jasa Peralatan Test di Serpong'],
            ['i_id_contr' => 36, 'c_org_contr' => 'IT0000', 'i_contr' => '01/PRY/2002', 'i_contr_ref' => '01/PRY/2002', 'n_contr_proj' => 'PEMBANGUNAN SI SEMARANG JAWA TENGAH'],
            ['i_id_contr' => 37, 'c_org_contr' => 'TD0000', 'i_contr' => '73/PO/SBU-ITS/06/01', 'i_contr_ref' => '73/PO/SBU-ITS/06/01', 'n_contr_proj' => 'Diesel Piston Tunggal 20 PK'],
            ['i_id_contr' => 38, 'c_org_contr' => 'LM0000', 'i_contr' => '11/SPK-Re/Log-KDL/0802', 'i_contr_ref' => 'BA : tbd dan Com. Inv.: tbd', 'n_contr_proj' => 'Battery Charger Repair'],
            ['i_id_contr' => 39, 'c_org_contr' => 'FT0000', 'i_contr' => 'GSE/27/04/2002', 'i_contr_ref' => 'QUOTATION No. 011/Q/FTC/04/02', 'n_contr_proj' => 'PEMBUATAN TOW-HEAD TOW BAR B737-200'],
            ['i_id_contr' => 40, 'c_org_contr' => 'FT0000', 'i_contr' => 'PO No. GSE/001/01/2002', 'i_contr_ref' => 'QUOTATION No..032/Q/FTC/12/01', 'n_contr_proj' => 'PEMBUATAN TOWBAR FOKKER-100 (F100SPC) & B-737-200 (B737-200SPC)'],
            ['i_id_contr' => 41, 'c_org_contr' => 'FT0000', 'i_contr' => 'PO No. F/037/04/02', 'i_contr_ref' => 'PO No. F/037/04/02', 'n_contr_proj' => 'PENGUJIAN PERFORMANCE QUALIFICATION TEST STERILMATIC'],
            ['i_id_contr' => 42, 'c_org_contr' => 'FT0000', 'i_contr' => '94/KO1.9.11/DN/2002', 'i_contr_ref' => '94/KO1.9.11/DN/2002', 'n_contr_proj' => 'PERBANTUAN TENAGA AHLI PERANGKAT LUNAK'],
            ['i_id_contr' => 43, 'c_org_contr' => 'FT0000', 'i_contr' => 'SPK/001/CSM/I&M/DIR/V/02', 'i_contr_ref' => 'SPK/001/CSM/I&M/DIR/V/02', 'n_contr_proj' => 'PEMASANGAN INSTALASI TRACKING & COMMISSIONING PERANGKAT CSM-LINK'],
            ['i_id_contr' => 44, 'c_org_contr' => 'FT0000', 'i_contr' => '001/K/DIR-KU/PAS/2002', 'i_contr_ref' => '0148/HE7000/01/2000', 'n_contr_proj' => 'PERBANT. PERS. PENERBANG PT. DI DI PT. PAS'],
            ['i_id_contr' => 45, 'c_org_contr' => 'IN0000', 'i_contr' => '045/F/02', 'i_contr_ref' => '045/F/02', 'n_contr_proj' => 'Blanket C212'],
            ['i_id_contr' => 46, 'c_org_contr' => 'IN0000', 'i_contr' => '047/F/02', 'i_contr_ref' => '047/F/02', 'n_contr_proj' => 'Blanket C212'],
            ['i_id_contr' => 47, 'c_org_contr' => 'IN0000', 'i_contr' => '059/F/02', 'i_contr_ref' => '059/F/02', 'n_contr_proj' => 'Engine Harness'],
            ['i_id_contr' => 48, 'c_org_contr' => 'FT0000', 'i_contr' => 'VZ/Perj-1022/2001', 'i_contr_ref' => 'QUOTATION/86/FT5000/09/2001', 'n_contr_proj' => 'TRAINING ELECT& JARINGAN'],
            ['i_id_contr' => 49, 'c_org_contr' => 'IT0000', 'i_contr' => '4/7/DSDM', 'i_contr_ref' => '4/7/DSDM', 'n_contr_proj' => 'PELATIHAN ORACLE DATA BASE'],
            ['i_id_contr' => 50, 'c_org_contr' => 'IT0000', 'i_contr' => 'SP-429/VI/2002', 'i_contr_ref' => 'SP-429/VI/2002', 'n_contr_proj' => 'PERPANJANGAN SIUDIN PERURI'],
        ];

        foreach ($data as $row) {
            Tmcontr::create($row);
        }
    }
}
