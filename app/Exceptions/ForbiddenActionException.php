<?php

namespace App\Exceptions;

use RuntimeException;

class ForbiddenActionException extends RuntimeException
{
    public function __construct(
        ?string $message = null,
        ?string $action = null,
        ?\Throwable $previous = null,
    ) {
        $message ??= 'Anda tidak memiliki izin untuk melakukan aksi ini.';

        if ($action !== null) {
            $message .= sprintf(' (Aksi: %s)', $action);
        }

        parent::__construct(message: $message, previous: $previous);
    }
}
