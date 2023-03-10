<?php

declare(strict_types=1);

namespace App\Model\Transaction;

enum TransactionType: string
{
    case Deposit = 'deposit';
    case Withdrawal = 'withdrawal';
}
