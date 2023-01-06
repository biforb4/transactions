<?php

declare(strict_types=1);

namespace App\Tests\UseCases\Transaction;

use App\Model\Transaction\Money;
use App\Model\Transaction\PaymentMethod;
use App\Model\Transaction\Transaction;
use App\Model\Transaction\TransactionType;

class TransactionFixture
{
    public static function aTransaction(): Transaction
    {
        return Transaction::of(
            new Money('USD', 100),
            new Money('EUR', 100),
            PaymentMethod::Bank,
            TransactionType::Deposit,
            1.0,
            '127.0.0.1'
        );
    }
}
