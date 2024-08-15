<?php

declare(strict_types=1);

namespace App\app\http;

use App\app\exceptions\ExchangeRateException;

class ExchangeApiClient implements ApiInterface
{
    private const BASE_URI = 'https://api.exchangeratesapi.io/v1/latest?';

    private string $apiKey;
    private string $baseCurrency;
    private string $ratesFilePath;

    public function __construct(array $configs)
    {
        $this->baseCurrency  = $configs['baseCurrency'];
        $this->apiKey        = $configs['apiKey'];
        $this->ratesFilePath = $configs['ratesFilePath'];
    }

    public function getExchangeRates(): array
    {
        $url = self::BASE_URI . http_build_query(['access_key' => $this->apiKey, 'base' => $this->baseCurrency, 'format' => 1]);

        $rates = json_decode((string) $this->makeApiRequest($url), true);

        if (!is_array($rates)) {
            throw new ExchangeRateException($url . ' (API key expired or invalid JSON)');
        }

        return $rates;
    }

    /**
     * @param string $url
     * @return false|string
     */
    public function makeApiRequest(string $url): string|false
    {
        return ENV_DEV
            ? file_get_contents(ROOT_PATH . $this->ratesFilePath)
            : file_get_contents($url); // TODO: replace with Guzzle Http Client
    }
}
