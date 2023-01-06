<?php

declare(strict_types=1);

namespace App\UseCases\Transaction\ChangeTargetCurrency;

use App\UseCases\UseCase;

readonly class ChangeTargetCurrency implements UseCase
{

    public function __construct(public string $id, public string $targetCurrency)
    {
    }
}
