<?php

namespace App\Exceptions;

use RuntimeException;

class InsufficientBalanceException extends RuntimeException
{
    public function __construct(
        ?string $message = null,
        ?float $balance = null,
        ?float $required = null,
        ?\Throwable $previous = null,
    ) {
        $message ??= 'Saldo tidak mencukupi untuk melakukan transaksi ini.';

        if ($balance !== null && $required !== null) {
            $message .= sprintf(' (Saldo: %s, Diperlukan: %s)', number_format($balance, 2, ',', '.'), number_format($required, 2, ',', '.'));
        }

        parent::__construct(message: $message, previous: $previous);
    }
}
