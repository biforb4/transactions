<?php

declare(strict_types=1);

namespace App\Model\Transaction;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[Entity]
class Transaction
{
    #[Id]
    #[Column(type: UuidType::NAME)]
    private Uuid $id;
    #[Column(type: 'string', enumType: PaymentMethod::class)]
    private PaymentMethod $paymentMethod;
    #[Column(type: 'string', enumType: TransactionType::class)]
    private TransactionType $transactionType;
    #[Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $timestamp;
    #[Column(type: 'integer')]
    private int $baseAmount;
    #[Column(type: 'string')]
    private string $baseCurrency;
    #[Column(type: 'integer')]
    private int $targetAmount;
    #[Column(type: 'string')]
    private string $targetCurrency;
    #[Column(type: 'float')]
    private float $exchangeRate;
    #[Column(type: 'string')]
    private string $ipAddress;

    private function __construct() {}

    public static function of(
        Money $base,
        Money $target,
        PaymentMethod $paymentMethod,
        TransactionType $transactionType,
        float $exchangeRate,
        string $ipAddress
    ): Transaction {
        $transaction = new Transaction();
        $transaction->id = Uuid::v4();
        $transaction->paymentMethod = $paymentMethod;
        $transaction->transactionType = $transactionType;
        $transaction->timestamp = new \DateTimeImmutable('now');
        $transaction->baseAmount = $base->getAmount();
        $transaction->baseCurrency = $base->getCurrencyCode();
        $transaction->targetAmount = $target->getAmount();
        $transaction->targetCurrency = $target->getCurrencyCode();
        $transaction->exchangeRate = $exchangeRate;
        $transaction->ipAddress = $ipAddress;

        return $transaction;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getPaymentMethod(): PaymentMethod
    {
        return $this->paymentMethod;
    }

    public function getTransactionType(): TransactionType
    {
        return $this->transactionType;
    }

    public function getTimestamp(): \DateTimeImmutable
    {
        return $this->timestamp;
    }

    public function getBaseAmount(): int
    {
        return $this->baseAmount;
    }

    public function getBaseCurrency(): string
    {
        return $this->baseCurrency;
    }

    public function getTargetAmount(): int
    {
        return $this->targetAmount;
    }

    public function getTargetCurrency(): string
    {
        return $this->targetCurrency;
    }

    public function getExchangeRate(): float
    {
        return $this->exchangeRate;
    }

    public function getIpAddress(): string
    {
        return $this->ipAddress;
    }

    public function getBaseMoney(): Money
    {
        return new Money($this->getBaseCurrency(), $this->getBaseAmount());
    }

    public function getTargetMoney(): Money
    {
        return new Money($this->getTargetCurrency(), $this->getTargetAmount());
    }

    public function updateDestinationCurrency(Money $target, float $exchangeRate): void
    {
        $this->exchangeRate = $exchangeRate;
        $this->targetCurrency = $target->getCurrencyCode();
        $this->targetAmount = $target->getAmount();
    }

}
