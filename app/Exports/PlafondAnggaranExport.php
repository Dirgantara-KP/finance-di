<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class PlafondAnggaranExport implements
    FromArray,
    WithColumnWidths,
    WithDrawings,
    WithEvents
{
    protected array $data;
    protected array $info;

    public function __construct(array $data, array $info = [])
    {
        $this->data = $data;
        $this->info = $info;
    }

    /*
    |--------------------------------------------------------------------------
    | DATA EXCEL
    |--------------------------------------------------------------------------
    */

    public function array(): array
    {
        $months = [
            'JAN',
            'FEB',
            'MAR',
            'APR',
            'MEI',
            'JUN',
            'JUL',
            'AGS',
            'SEP',
            'OKT',
            'NOV',
            'DES',
        ];

        /*
        |--------------------------------------------------------------------------
        | WAKTU EXPORT - WIB
        |--------------------------------------------------------------------------
        */

        $tanggalExport = now('Asia/Jakarta')->format('d/m/Y H:i');

        return [

            /*
            |--------------------------------------------------------------------------
            | HEADER
            |--------------------------------------------------------------------------
            */

            // Row 1 - Logo
            [
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
            ],

            // Row 2 - Judul
            [
                '',
                'PLAFOND ANGGARAN',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
            ],

            // Row 3 - Subtitle
            [
                '',
                'RINGKASAN SALDO ANGGARAN TAHUN ' . ($this->info['tahun'] ?? ''),
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
            ],

            // Row 4 - Garis pemisah
            array_fill(0, 14, ''),

            /*
            |--------------------------------------------------------------------------
            | INFORMASI
            |--------------------------------------------------------------------------
            */

            // Row 5
            [
                'Tahun Anggaran',
                ':',
                $this->info['tahun'] ?? '-',
                '',
                '',
                '',
                '',
                'Nama Program',
                ':',
                $this->info['namaProgram'] ?? '-',
                '',
                '',
                '',
                '',
            ],

            // Row 6
            [
                'Organisasi',
                ':',
                $this->info['organisasi'] ?? '-',
                '',
                '',
                '',
                '',
                'Nama Sandi',
                ':',
                $this->info['namaSandi'] ?? '-',
                '',
                '',
                '',
                '',
            ],

            // Row 7
            [
                'Sandi',
                ':',
                $this->info['sandi'] ?? '-',
                '',
                '',
                '',
                '',
                'Tanggal Export',
                ':',
                $tanggalExport,
                '',
                '',
                '',
                '',
            ],

            // Row 8
            [
                'PON',
                ':',
                $this->info['pon'] ?? '-',
                '',
                '',
                '',
                '',
                'Dicetak Oleh',
                ':',
                $this->info['printedBy'] ?? 'System',
                '',
                '',
                '',
                '',
            ],

            // Row 9
            [
                'Kontrak',
                ':',
                $this->info['kontrak'] ?? '-',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
            ],

            // Row 10 - Spacer
            array_fill(0, 14, ''),

            /*
            |--------------------------------------------------------------------------
            | HEADER TABEL
            |--------------------------------------------------------------------------
            */

            // Row 11
            [
                'KETERANGAN',
                ...$months,
                'TOTAL',
            ],

            /*
            |--------------------------------------------------------------------------
            | DATA
            |--------------------------------------------------------------------------
            */

            // Row 12
            $this->data[0] ?? array_fill(0, 14, ''),

            // Row 13
            $this->data[1] ?? array_fill(0, 14, ''),

            // Row 14
            $this->data[2] ?? array_fill(0, 14, ''),

            // Row 15 - Spacer
            array_fill(0, 14, ''),

            /*
            |--------------------------------------------------------------------------
            | CATATAN
            |--------------------------------------------------------------------------
            */

            // Row 16
            [
                'Catatan :',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
            ],

            // Row 17
            [
                '• Laporan ini merupakan ringkasan saldo anggaran per bulan.',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
            ],

            // Row 18
            [
                '• Total dihitung berdasarkan data yang dimuat.',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
            ],

            /*
            |--------------------------------------------------------------------------
            | FOOTER
            |--------------------------------------------------------------------------
            */

            // Row 19
            [
                '',
                '--- Terima kasih ---',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
            ],

            // Row 19
            [
                '',
                '--- Terima kasih ---',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
            ],
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | COLUMN WIDTH
    |--------------------------------------------------------------------------
    */

    public function columnWidths(): array
    {
        return [

            // Keterangan
            'A' => 22,

            // Jan - Des
            'B' => 15,
            'C' => 15,
            'D' => 15,
            'E' => 15,
            'F' => 15,
            'G' => 15,
            'H' => 15,
            'I' => 15,
            'J' => 15,
            'K' => 15,
            'L' => 15,
            'M' => 15,

            // Total
            'N' => 17,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | LOGO
    |--------------------------------------------------------------------------
    */

    public function drawings(): array
    {
        $logoPath = public_path('images/logo-perusahaan.png');

        if (! file_exists($logoPath)) {
            return [];
        }

        $drawing = new Drawing();

        $drawing->setName('Logo Perusahaan');
        $drawing->setDescription('Logo Perusahaan');

        /*
        |--------------------------------------------------------------------------
        | LOGO DIPOSISIKAN DI TENGAH
        |--------------------------------------------------------------------------
        */

        $drawing->setPath($logoPath, false);

        // Ukuran logo
        $drawing->setHeight(85);

        /*
        |--------------------------------------------------------------------------
        | Anchor logo
        |--------------------------------------------------------------------------
        |
        | H1 berada di area tengah spreadsheet.
        |
        */

        $drawing->setCoordinates('H1');

        // Geser sedikit ke kiri supaya benar-benar berada di tengah
        $drawing->setOffsetX(-25);
        $drawing->setOffsetY(5);

        return [$drawing];
    }

    /*
    |--------------------------------------------------------------------------
    | STYLING
    |--------------------------------------------------------------------------
    */

    public function registerEvents(): array
    {
        return [

            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();

                /*
                |--------------------------------------------------------------------------
                | MERGE HEADER
                |--------------------------------------------------------------------------
                */

                $sheet->mergeCells('B2:N2');
                $sheet->mergeCells('B3:N3');

                /*
                |--------------------------------------------------------------------------
                | MERGE INFORMASI
                |--------------------------------------------------------------------------
                */

                $sheet->mergeCells('C5:G5');
                $sheet->mergeCells('J5:N5');

                $sheet->mergeCells('C6:G6');
                $sheet->mergeCells('J6:N6');

                $sheet->mergeCells('C7:G7');
                $sheet->mergeCells('J7:N7');

                $sheet->mergeCells('C8:G8');
                $sheet->mergeCells('J8:N8');

                $sheet->mergeCells('C9:G9');

                /*
                |--------------------------------------------------------------------------
                | MERGE CATATAN
                |--------------------------------------------------------------------------
                */

                $sheet->mergeCells('A16:N16');
                $sheet->mergeCells('A17:N17');
                $sheet->mergeCells('A18:N18');

                /*
                |--------------------------------------------------------------------------
                | MERGE FOOTER
                |--------------------------------------------------------------------------
                */

                $sheet->mergeCells('B19:N19');

                /*
                |--------------------------------------------------------------------------
                | ROW HEIGHT
                |--------------------------------------------------------------------------
                */

                // Logo
                $sheet->getRowDimension(1)->setRowHeight(70);

                // Judul
                $sheet->getRowDimension(2)->setRowHeight(32);

                // Subtitle
                $sheet->getRowDimension(3)->setRowHeight(24);

                // Garis
                $sheet->getRowDimension(4)->setRowHeight(5);

                // Informasi
                for ($row = 5; $row <= 9; $row++) {
                    $sheet->getRowDimension($row)->setRowHeight(24);
                }

                // Spacer
                $sheet->getRowDimension(10)->setRowHeight(8);

                // Header tabel
                $sheet->getRowDimension(11)->setRowHeight(30);

                // Data tabel
                $sheet->getRowDimension(12)->setRowHeight(28);
                $sheet->getRowDimension(13)->setRowHeight(28);
                $sheet->getRowDimension(14)->setRowHeight(28);

                /*
                |--------------------------------------------------------------------------
                | JUDUL
                |--------------------------------------------------------------------------
                */

                $sheet->getStyle('B2:N2')->applyFromArray([

                    'font' => [
                        'bold' => true,
                        'size' => 20,
                        'color' => [
                            'rgb' => '17365D',
                        ],
                    ],

                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],

                ]);

                /*
                |--------------------------------------------------------------------------
                | SUBTITLE
                |--------------------------------------------------------------------------
                */

                $sheet->getStyle('B3:N3')->applyFromArray([

                    'font' => [
                        'bold' => true,
                        'size' => 12,
                        'color' => [
                            'rgb' => '17365D',
                        ],
                    ],

                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],

                ]);

                /*
                |--------------------------------------------------------------------------
                | GARIS BAWAH HEADER
                |--------------------------------------------------------------------------
                */

                $sheet->getStyle('A4:N4')->applyFromArray([

                    'borders' => [
                        'bottom' => [
                            'borderStyle' => Border::BORDER_MEDIUM,
                            'color' => [
                                'rgb' => '17365D',
                            ],
                        ],
                    ],

                ]);

                /*
                |--------------------------------------------------------------------------
                | INFORMASI
                |--------------------------------------------------------------------------
                */

                $sheet->getStyle('A5:N9')->applyFromArray([

                    'font' => [
                        'size' => 10,
                        'color' => [
                            'rgb' => '000000',
                        ],
                    ],

                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],

                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => [
                                'rgb' => 'D9D9D9',
                            ],
                        ],
                    ],

                ]);

                /*
                |--------------------------------------------------------------------------
                | LABEL INFORMASI KIRI
                |--------------------------------------------------------------------------
                */

                $sheet->getStyle('A5:A9')->applyFromArray([

                    'font' => [
                        'bold' => true,
                        'color' => [
                            'rgb' => '17365D',
                        ],
                    ],

                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => [
                            'rgb' => 'DCE6F1',
                        ],
                    ],

                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],

                ]);

                /*
                |--------------------------------------------------------------------------
                | LABEL INFORMASI KANAN
                |--------------------------------------------------------------------------
                */

                $sheet->getStyle('H5:H8')->applyFromArray([

                    'font' => [
                        'bold' => true,
                        'color' => [
                            'rgb' => '17365D',
                        ],
                    ],

                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => [
                            'rgb' => 'DCE6F1',
                        ],
                    ],

                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],

                ]);

                /*
                |--------------------------------------------------------------------------
                | TITIK DUA
                |--------------------------------------------------------------------------
                */

                $sheet->getStyle('B5:B9')->applyFromArray([

                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],

                ]);

                $sheet->getStyle('I5:I8')->applyFromArray([

                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],

                ]);

                /*
                |--------------------------------------------------------------------------
                | VALUE INFORMASI
                |--------------------------------------------------------------------------
                |
                | SEMUA VALUE RATA KIRI.
                | Termasuk Tahun Anggaran.
                |
                */

                $sheet->getStyle('C5:G9')->applyFromArray([

                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],

                ]);

                $sheet->getStyle('J5:N8')->applyFromArray([

                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],

                ]);

                /*
                |--------------------------------------------------------------------------
                | HEADER TABEL
                |--------------------------------------------------------------------------
                */

                $sheet->getStyle('A11:N11')->applyFromArray([

                    'font' => [
                        'bold' => true,
                        'size' => 10,
                        'color' => [
                            'rgb' => 'FFFFFF',
                        ],
                    ],

                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => [
                            'rgb' => '17365D',
                        ],
                    ],

                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],

                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => [
                                'rgb' => 'FFFFFF',
                            ],
                        ],
                    ],

                ]);

                /*
                |--------------------------------------------------------------------------
                | TABLE BODY
                |--------------------------------------------------------------------------
                */

                $sheet->getStyle('A12:N14')->applyFromArray([

                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],

                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => [
                                'rgb' => 'D9D9D9',
                            ],
                        ],
                    ],

                ]);

                /*
                |--------------------------------------------------------------------------
                | LABEL BARIS
                |--------------------------------------------------------------------------
                */

                $sheet->getStyle('A12:A14')->applyFromArray([

                    'font' => [
                        'bold' => true,
                    ],

                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],

                ]);

                /*
                |--------------------------------------------------------------------------
                | SALDO AWAL
                |--------------------------------------------------------------------------
                */

                $sheet->getStyle('A12:N12')->applyFromArray([

                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => [
                            'rgb' => 'EAF2F8',
                        ],
                    ],

                ]);

                /*
                |--------------------------------------------------------------------------
                | PENAMBAHAN
                |--------------------------------------------------------------------------
                */

                $sheet->getStyle('A13:N13')->applyFromArray([

                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => [
                            'rgb' => 'EAF4E3',
                        ],
                    ],

                ]);

                /*
                |--------------------------------------------------------------------------
                | SALDO AKHIR
                |--------------------------------------------------------------------------
                */

                $sheet->getStyle('A14:N14')->applyFromArray([

                    'font' => [
                        'bold' => true,
                    ],

                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => [
                            'rgb' => 'FFF2CC',
                        ],
                    ],

                ]);

                /*
                |--------------------------------------------------------------------------
                | ANGKA
                |--------------------------------------------------------------------------
                */

                $sheet->getStyle('B12:N14')->applyFromArray([

                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_RIGHT,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],

                ]);

                /*
                |--------------------------------------------------------------------------
                | FORMAT ANGKA
                |--------------------------------------------------------------------------
                */

                $sheet->getStyle('B12:N14')
                    ->getNumberFormat()
                    ->setFormatCode('#,##0');

                /*
                |--------------------------------------------------------------------------
                | TOTAL
                |--------------------------------------------------------------------------
                */

                $sheet->getStyle('N11:N14')->applyFromArray([

                    'font' => [
                        'bold' => true,
                    ],

                ]);

                /*
                |--------------------------------------------------------------------------
                | CATATAN
                |--------------------------------------------------------------------------
                */

                $sheet->getStyle('A16:N18')->applyFromArray([

                    'font' => [
                        'size' => 9,
                        'color' => [
                            'rgb' => '333333',
                        ],
                    ],

                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],

                ]);

                $sheet->getStyle('A16')->applyFromArray([

                    'font' => [
                        'bold' => true,
                        'size' => 10,
                        'color' => [
                            'rgb' => '17365D',
                        ],
                    ],

                ]);

                /*
                |--------------------------------------------------------------------------
                | FOOTER
                |--------------------------------------------------------------------------
                */

                $sheet->getStyle('B19:N19')->applyFromArray([

                    'font' => [
                        'bold' => true,
                        'italic' => true,
                        'size' => 10,
                        'color' => [
                            'rgb' => '17365D',
                        ],
                    ],

                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],

                ]);

                /*
                |--------------------------------------------------------------------------
                | FREEZE PANE
                |--------------------------------------------------------------------------
                */

                $sheet->freezePane('B12');

                /*
                |--------------------------------------------------------------------------
                | PAGE SETUP
                |--------------------------------------------------------------------------
                */

                $sheet->getPageSetup()->setOrientation(
                    PageSetup::ORIENTATION_LANDSCAPE
                );

                $sheet->getPageSetup()->setPaperSize(
                    PageSetup::PAPERSIZE_A4
                );

                $sheet->getPageSetup()->setFitToWidth(1);
                $sheet->getPageSetup()->setFitToHeight(0);

                /*
                |--------------------------------------------------------------------------
                | MARGIN
                |--------------------------------------------------------------------------
                */

                $sheet->getPageMargins()->setTop(0.3);
                $sheet->getPageMargins()->setBottom(0.3);
                $sheet->getPageMargins()->setLeft(0.3);
                $sheet->getPageMargins()->setRight(0.3);

                /*
                |--------------------------------------------------------------------------
                | PRINT AREA
                |--------------------------------------------------------------------------
                */

                $sheet->getPageSetup()->setPrintArea('A1:N19');

                /*
                |--------------------------------------------------------------------------
                | VIEW
                |--------------------------------------------------------------------------
                */

                $sheet->setShowGridlines(false);
            },
        ];
    }
}