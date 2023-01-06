<?php

declare(strict_types=1);

namespace App\UseCases\Transaction\ChangeTargetCurrency;

use App\Model\Transaction\Transaction;
use App\UseCases\Transaction\ExchangeRateProviderInterface;
use App\UseCases\Transaction\TransactionRepositoryInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class ChangeTargetCurrencyHandler
{
    public function __construct(
        private ExchangeRateProviderInterface $exchangeRateProvider,
        private TransactionRepositoryInterface $transactionRepository
    ) {
    }

    public function __invoke(ChangeTargetCurrency $changeTargetCurrency): Transaction
    {
        $transaction = $this->transactionRepository->findById($changeTargetCurrency->id);
        if ($transaction === null) {
            throw new NotFoundHttpException(sprintf('Transaction with id %s not found', $changeTargetCurrency->id));
        }
        $base = $transaction->getBaseMoney();
        if ($base->getCurrencyCode() === $changeTargetCurrency->targetCurrency) {
            throw new BadRequestHttpException(
                sprintf('Cannot set target currency to be the same as base (%s)', $base->getCurrencyCode())
            );
        }
        $exchangeRate = $this->exchangeRateProvider->exchangeRate(
            $base->getCurrencyCode(),
            $changeTargetCurrency->targetCurrency
        );
        $target = $base->convertTo($changeTargetCurrency->targetCurrency, $exchangeRate);

        $transaction->updateDestinationCurrency($target, $exchangeRate);
        $this->transactionRepository->save($transaction);

        return $transaction;
    }
}
