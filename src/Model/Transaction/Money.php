<?php

declare(strict_types=1);

namespace App\Model\Transaction;

readonly class Money
{
    public function __construct(private string $currencyCode, private int $amount)
    {
    }

    public function convertTo(string $currencyCode, float $exchangeRate): Money
    {
        return new Money($currencyCode, (int)floor($this->amount * $exchangeRate));
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getCurrencyCode(): string
    {
        return $this->currencyCode;
    }
}
