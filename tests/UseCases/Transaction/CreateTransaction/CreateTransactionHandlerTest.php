<?php

declare(strict_types=1);

namespace App\Tests\UseCases\Transaction\CreateTransaction;

use App\Model\Transaction\PaymentMethod;
use App\Model\Transaction\TransactionType;
use App\UseCases\Transaction\CreateTransaction\CreateTransaction;
use App\UseCases\Transaction\CreateTransaction\CreateTransactionHandler;
use App\UseCases\Transaction\ExchangeRateProviderInterface;
use App\UseCases\Transaction\TransactionRepositoryInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class CreateTransactionHandlerTest extends TestCase
{

    private MockObject|TransactionRepositoryInterface $repository;
    private ExchangeRateProviderInterface|MockObject $exchangeRateProvider;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(TransactionRepositoryInterface::class);
        $this->exchangeRateProvider = $this->createMock(ExchangeRateProviderInterface::class);
    }

    public function testShouldThrowBadRequestExceptionWhenBasAndTargetCurrencenciesAreTheSame()
    {
        //when && then
        $this->expectException(BadRequestHttpException::class);
        $sut = new CreateTransactionHandler($this->exchangeRateProvider, $this->repository);
        $sut($this->createUseCase('PLN', 'PLN'));
    }

    public function testShouldCreateNewTransaction()
    {
        //given
        $this->exchangeRateProvider->method('exchangeRate')->willReturn(2.0);
        $this->repository->expects($this->once())->method('save');

        //when
        $sut = new CreateTransactionHandler($this->exchangeRateProvider, $this->repository);
        $result = $sut($this->createUseCase('PLN', 'EUR'));

        //then
        $this->assertSame(2.0, $result->getExchangeRate());
        $this->assertSame('PLN', $result->getBaseCurrency());
        $this->assertSame('EUR', $result->getTargetCurrency());
    }

    private function createUseCase(string $baseCurrency, string $targetCurrency): CreateTransaction
    {
        return new CreateTransaction(
            100,
            $baseCurrency,
            $targetCurrency,
            PaymentMethod::Bank,
            TransactionType::Deposit,
            '127.0.0.1'
        );
    }
}
