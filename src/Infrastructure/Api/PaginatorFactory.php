<?php

declare(strict_types=1);

namespace App\Infrastructure\Api;

use App\Infrastructure\Persistence\Doctrine\PaginatedRepositoryInterface;
use App\Model\Transaction\Transaction;
use Pagerfanta\Adapter\FixedAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @template T
 */
class PaginatorFactory
{
    private const PER_PAGE = 10;
    private const FIRST_PAGE = 1;
    private Request $request;
    /** @var PaginatedRepositoryInterface<T> */
    private PaginatedRepositoryInterface $repository;

    public function __construct(
        RequestStack $requestStack,
        PaginatedRepositoryInterface $repository
    ) {
        $request = $requestStack->getCurrentRequest();
        if ($request === null) {
            throw new \LogicException('Routes can be generated only in the context of request');
        }
        $this->request = $request;
        $this->repository = $repository;
    }

    public function create(): Pagerfanta
    {
        $page = $this->request->query->getInt('page', self::FIRST_PAGE);
        $perPage = $this->request->query->getInt('perPage', self::PER_PAGE);
        $offset = ($page - 1) * $perPage;
        $paginator = new Pagerfanta(
            new FixedAdapter($this->repository->count(), $this->repository->getSlice($offset, $perPage))
        );
        $paginator->setMaxPerPage($perPage);
        $paginator->setCurrentPage($page);
        return $paginator;
    }

}
