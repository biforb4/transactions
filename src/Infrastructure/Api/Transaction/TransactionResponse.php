<?php

declare(strict_types=1);

namespace App\Infrastructure\Api\Transaction;

use App\Model\Transaction\Transaction;
use League\Fractal\TransformerAbstract;

class TransactionResponse extends TransformerAbstract
{
    public function transform(Transaction $transaction): array
    {
        return [
            'id' => $transaction->getId(),
            'paymentMethod' => $transaction->getPaymentMethod()->value,
            'transactionType' => $transaction->getTransactionType()->value,
            'timestamp' => $transaction->getTimestamp()->getTimestamp(),
            'baseAmount' => $transaction->getBaseAmount() / 100,
            'baseCurrency' => $transaction->getBaseCurrency(),
            'targetAmount' => $transaction->getTargetAmount() / 100,
            'targetCurrency' => $transaction->getTargetCurrency(),
            'exchangeRate' => $transaction->getExchangeRate(),
            'ipAddress' => $transaction->getIpAddress(),
        ];
    }
}
