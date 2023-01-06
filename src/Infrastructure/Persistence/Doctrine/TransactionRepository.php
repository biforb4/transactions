<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine;

use App\Model\Transaction\Transaction;
use App\UseCases\Transaction\TransactionRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @template-implements PaginatedRepositoryInterface<Transaction>
 * @template-extends  ServiceEntityRepository<Transaction>
 */
class TransactionRepository extends ServiceEntityRepository implements TransactionRepositoryInterface, PaginatedRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transaction::class);
    }

    public function findAll(): array
    {
        return parent::findAll();
    }

    public function save(Transaction $transaction): void
    {
        $this->_em->persist($transaction);
        $this->_em->flush();
    }

    public function count($criteria = []): int
    {
        return parent::count($criteria);
    }

    public function getSlice($offset, $length): array
    {
        return $this->createQueryBuilder('transaction')
            ->setFirstResult($offset)
            ->setMaxResults($length)
            ->getQuery()
            ->getResult();
    }

    public function findById(string $id): ?Transaction
    {
        return $this->find($id);
    }
}
