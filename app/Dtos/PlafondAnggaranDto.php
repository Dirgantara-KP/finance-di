<?php

namespace App\Dtos;

final readonly class PlafondAnggaranDto
{
    /**
     * @param  array<int, int>  $saldoMonth
     * @param  array<int, int>  $addMonth
     */
    public function __construct(
        public string $tahun = '',
        public string $org = '',
        public string $sandi = '',
        public string $pon = '',
        public string $orgContr = '',
        public string $iContr = '',
        public array $saldoMonth = [],
        public array $addMonth = [],
        public string $pgm = '',
        public string $pgmSub = '',
        public string $entry = '',
        public string $orgCenter = '',
    ) {}
}
