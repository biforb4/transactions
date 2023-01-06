<?php

declare(strict_types=1);

namespace App\Infrastructure\Api\Transaction;

use App\Infrastructure\Api\PageRouteGenerator;
use App\Infrastructure\Api\PaginatorFactory;
use App\Model\Transaction\Transaction;
use League\Fractal\Manager;
use League\Fractal\Pagination\PagerfantaPaginatorAdapter;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class GetTransaction extends AbstractController
{
    /**  */
    public function __construct(
        private Manager $fractal,
        private PaginatorFactory $paginatorFactory,
        private PageRouteGenerator $pageRouteGenerator,
    ) {
    }

    #[Route(path: "/transactions", methods: ["GET"])]
    public function list(): JsonResponse
    {
        $paginator = $this->paginatorFactory->create();

        $paginatorAdapter = new PagerfantaPaginatorAdapter($paginator, $this->pageRouteGenerator);
        $resource = new Collection($paginator->getCurrentPageResults(), new TransactionResponse());
        $resource->setPaginator($paginatorAdapter);

        return new JsonResponse($this->fractal->createData($resource)->toArray());
    }

    #[Route(path: "/transactions/{id}", methods: ["GET"])]
    public function one(Transaction $transaction): JsonResponse
    {
        $transactionResource = new Item($transaction, new TransactionResponse());

        return new JsonResponse($this->fractal->createData($transactionResource)->toArray());
    }
}
