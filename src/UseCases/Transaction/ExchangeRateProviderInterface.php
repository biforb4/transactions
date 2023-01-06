<?php

declare(strict_types=1);

namespace App\UseCases\Transaction;

use Brick\Money\Currency;

interface ExchangeRateProviderInterface
{
    public function exchangeRate(string $from, string $to): float;
}
