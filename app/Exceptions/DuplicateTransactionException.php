<?php

namespace App\Exceptions;

use RuntimeException;

class DuplicateTransactionException extends RuntimeException
{
    public function __construct(
        ?string $message = null,
        ?string $reference = null,
        ?\Throwable $previous = null,
    ) {
        $message ??= 'Transaksi dengan data yang sama sudah ada dalam sistem.';

        if ($reference !== null) {
            $message .= sprintf(' (Referensi: %s)', $reference);
        }

        parent::__construct(message: $message, previous: $previous);
    }
}
