<?php

declare(strict_types=1);

namespace App\Model\Transaction;

enum PaymentMethod: string
{
    case Card = 'card';
    case Bank = 'bank';
}
