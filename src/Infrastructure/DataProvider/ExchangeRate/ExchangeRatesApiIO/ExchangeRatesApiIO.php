<?php

declare(strict_types=1);

namespace App\Infrastructure\DataProvider\ExchangeRate\ExchangeRatesApiIO;

use App\UseCases\Transaction\ExchangeRateProviderInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\RequestOptions;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

const CACHE_TIME = 60*5;
readonly class ExchangeRatesApiIO implements ExchangeRateProviderInterface
{
    private const RESOURCE = 'latest';
    private const CACHE_KEY = 'currency_api_response';

    public function __construct(
        private ClientInterface $client,
        private LoggerInterface $logger,
        private CacheInterface $cache
    ) {
    }

    public function exchangeRate(string $from, string $to): float
    {
        $latestRates = $this->cache->get(self::CACHE_KEY, function (ItemInterface $item) use ($from) {
            $item->expiresAfter(CACHE_TIME);
            $response = $this->client->request(
                Request::METHOD_GET,
                self::RESOURCE,
                [
                    RequestOptions::QUERY => [
                        'base' => $from,
                    ],
                ]
            );
            return json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
        });


        if ($latestRates['success'] !== true) {
            $this->logger->error('Unexpected exchanges rates api response', ['response' => $latestRates]);
            throw new \UnexpectedValueException('Request to exchange rates api failed');
        }
        foreach ($latestRates['rates'] as $currency => $rate) {
            if ($currency === $to) {
                return $rate;
            }
        }

        $this->logger->error('Unexpected exchanges rates api response', ['response' => $latestRates]);
        throw new \UnexpectedValueException(
            sprintf('Currency %s not found in exchange rates api response', $to)
        );
    }
}
