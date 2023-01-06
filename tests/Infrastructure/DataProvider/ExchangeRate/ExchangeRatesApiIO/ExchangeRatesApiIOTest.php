<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\DataProvider\ExchangeRate\ExchangeRatesApiIO;

use App\Infrastructure\DataProvider\ExchangeRate\ExchangeRatesApiIO\ExchangeRatesApiIO;
use GuzzleHttp\ClientInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Contracts\Cache\CacheInterface;

class ExchangeRatesApiIOTest extends TestCase
{
    private ClientInterface|MockObject $client;
    private NullLogger $logger;
    private MockObject|CacheInterface $cache;

    protected function setUp(): void
    {
        $this->client = $this->createMock(ClientInterface::class);
        $this->logger = new NullLogger();
        $this->cache = $this->createMock(CacheInterface::class);
    }

    public function testShouldThrowWhenSuccessIsFalse()
    {
        //given
        $this->cache->method('get')->willReturn(['success' => false]);

        //when&&then
        $this->expectException(\UnexpectedValueException::class);
        $sut = new ExchangeRatesApiIO($this->client, $this->logger, $this->cache);
        $sut->exchangeRate('PLN', 'USD');
    }

    public function testShouldReturnRate()
    {
        //given
        $this->cache->method('get')->willReturn(['success' => true, 'rates' => ['USD' => 1.0]]);

        //when
        $sut = new ExchangeRatesApiIO($this->client, $this->logger, $this->cache);
        $result = $sut->exchangeRate('PLN', 'USD');

        //then
        $this->assertSame(1.0, $result);
    }

    public function testShouldThrowExceptionWhenTargetCurrencyNotFound()
    {
        //given
        $this->cache->method('get')->willReturn(['success' => true, 'rates' => ['USD' => 1.0]]);

        //when
        $sut = new ExchangeRatesApiIO($this->client, $this->logger, $this->cache);
        $this->expectException(\UnexpectedValueException::class);
        $sut->exchangeRate('PLN', 'GBP');
    }

}
