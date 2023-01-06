<?php

declare(strict_types=1);

namespace App\Infrastructure\Api\Transaction;

use App\UseCases\Transaction\CreateTransaction\CreateTransaction;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class PostTransaction extends AbstractController
{
    use HandleTrait;
    public function __construct(private MessageBusInterface $messageBus, private Manager $fractal)
    {
    }

    #[Route(path: '/transactions', methods: ["POST"])]
    public function create(CreateTransaction $useCase): JsonResponse
    {
        $transaction = $this->handle($useCase);

        $transactionResource = new Item($transaction, new TransactionResponse());

        return new JsonResponse($this->fractal->createData($transactionResource)->toArray());
    }
}
