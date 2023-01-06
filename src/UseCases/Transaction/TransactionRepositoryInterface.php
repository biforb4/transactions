<?php

declare(strict_types=1);

namespace App\UseCases\Transaction;

use App\Model\Transaction\Transaction;

interface TransactionRepositoryInterface
{
    /** @return list<Transaction> */
    public function findAll(): array;

    public function save(Transaction $transaction): void;

    public function findById(string $id): ?Transaction;
}
