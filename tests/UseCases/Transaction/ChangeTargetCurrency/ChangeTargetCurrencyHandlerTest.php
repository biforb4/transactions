<?php

declare(strict_types=1);

namespace App\Tests\UseCases\Transaction\ChangeTargetCurrency;

use App\Tests\UseCases\Transaction\TransactionFixture;
use App\UseCases\Transaction\ChangeTargetCurrency\ChangeTargetCurrency;
use App\UseCases\Transaction\ChangeTargetCurrency\ChangeTargetCurrencyHandler;
use App\UseCases\Transaction\ExchangeRateProviderInterface;
use App\UseCases\Transaction\TransactionRepositoryInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ChangeTargetCurrencyHandlerTest extends TestCase
{

    private MockObject|TransactionRepositoryInterface $repository;
    private ExchangeRateProviderInterface|MockObject $exchangeRateProvider;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(TransactionRepositoryInterface::class);
        $this->exchangeRateProvider = $this->createMock(ExchangeRateProviderInterface::class);
    }

    public function testShouldThrowNotFoundExceptionWhenNoTransactionFound(): void
    {
        //given
        $this->repository->method('findById')->with('id')->willReturn(null);

        //when && then
        $sut = new ChangeTargetCurrencyHandler($this->exchangeRateProvider, $this->repository);
        $this->expectException(NotFoundHttpException::class);
        $sut($this->createUseCase('PLN'));

    }

    public function testShouldThrowBadRequestExceptionWhenChangingTargetCurrencyToBeSameAsBase(): void
    {
        //given
        $transaction = TransactionFixture::aTransaction();
        $this->repository->method('findById')->with('id')->willReturn($transaction);

        //when && then
        $sut = new ChangeTargetCurrencyHandler($this->exchangeRateProvider, $this->repository);
        $this->expectException(BadRequestHttpException::class);
        $sut($this->createUseCase($transaction->getBaseCurrency()));

    }

    public function testShouldUpdateCurrencySuccessfully(): void
    {
        //given
        $transaction = TransactionFixture::aTransaction();
        $this->repository->method('findById')->with('id')->willReturn($transaction);
        $this->exchangeRateProvider->method('exchangeRate')->willReturn(2.0);
        $this->repository->expects($this->once())->method('save')->with($transaction);


        //when
        $sut = new ChangeTargetCurrencyHandler($this->exchangeRateProvider, $this->repository);
        $result = $sut($this->createUseCase('PLN'));

        //then
        $this->assertSame('PLN', $transaction->getTargetCurrency());
        $this->assertSame(2.0, $transaction->getExchangeRate());

    }

    private function createUseCase(string $currency): ChangeTargetCurrency
    {
        return new ChangeTargetCurrency(
            'id',
            $currency
        );
    }
}
