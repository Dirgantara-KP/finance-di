<?php

namespace App\Exceptions;

use RuntimeException;

class InvalidPeriodException extends RuntimeException
{
    public function __construct(
        ?string $message = null,
        ?string $period = null,
        ?\Throwable $previous = null,
    ) {
        $message ??= 'Periode akuntansi tidak valid atau sudah ditutup.';

        if ($period !== null) {
            $message .= sprintf(' (Periode: %s)', $period);
        }

        parent::__construct(message: $message, previous: $previous);
    }
}
