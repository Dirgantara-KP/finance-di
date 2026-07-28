<?php

namespace App\Exceptions;

use RuntimeException;

class ExportException extends RuntimeException
{
    public function __construct(
        ?string $message = null,
        ?string $format = null,
        ?\Throwable $previous = null,
    ) {
        $message ??= 'Gagal melakukan export data.';

        if ($format !== null) {
            $message .= sprintf(' (Format: %s)', $format);
        }

        parent::__construct(message: $message, previous: $previous);
    }
}
