<?php

declare(strict_types=1);

namespace App\UseCases\Transaction\CreateTransaction;

use App\Model\Transaction\PaymentMethod;
use App\Model\Transaction\TransactionType;
use App\UseCases\UseCase;

readonly class CreateTransaction implements UseCase
{
    public function __construct(
        public float $baseAmount,
        public string $baseCurrency,
        public string $targetCurrency,
        public PaymentMethod $paymentMethod,
        public TransactionType $transactionType,
        public string $ipAddress)
    {
    }
}
