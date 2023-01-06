<?php

declare(strict_types=1);

namespace App\Infrastructure\DataProvider\ExchangeRate\ExchangeRatesApiIO;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;

class ClientFactory
{
    public static function create(): ClientInterface
    {
        return new Client([
            'base_uri' => 'https://api.apilayer.com/exchangerates_data/',
            'headers' => [
                'apikey' => 'BHNXyhziZbn6Up9d7v10IX0J9SuVjvLp'
            ],
        ]);
    }
}
