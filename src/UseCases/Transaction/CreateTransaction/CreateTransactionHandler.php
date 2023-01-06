<?php

declare(strict_types=1);

namespace App\UseCases\Transaction\CreateTransaction;

use App\Model\Transaction\Money;
use App\Model\Transaction\Transaction;
use App\UseCases\Transaction\ExchangeRateProviderInterface;
use App\UseCases\Transaction\TransactionRepositoryInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class CreateTransactionHandler
{
    public function __construct(
        private ExchangeRateProviderInterface $exchangeRateProvider,
        private TransactionRepositoryInterface $transactionRepository
    ) {
    }

    public function __invoke(CreateTransaction $createTransaction): Transaction
    {
        if($createTransaction->baseCurrency === $createTransaction->targetCurrency) {
            throw new BadRequestHttpException('Base currency and target currency have to be different');
        }

        $exchangeRate = $this->exchangeRateProvider->exchangeRate(
            $createTransaction->baseCurrency,
            $createTransaction->targetCurrency
        );

        $base = new Money($createTransaction->baseCurrency, (int)(round($createTransaction->baseAmount * 100)));
        $target = $base->convertTo($createTransaction->targetCurrency, $exchangeRate);

        $transaction = Transaction::of(
            $base,
            $target,
            $createTransaction->paymentMethod,
            $createTransaction->transactionType,
            $exchangeRate,
            $createTransaction->ipAddress
        );

        $this->transactionRepository->save($transaction);

        return $transaction;
    }
}
