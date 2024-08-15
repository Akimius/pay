<?php

declare(strict_types=1);

namespace App\app\http;

use App\app\exceptions\BinListException;

/**
 * Bin list API limits API calls at 5 requests per hour
 */
class BinListApiClient implements ApiInterface
{
    private const BASE_URI = 'https://lookup.binlist.net/%d';

    public function getBinInfo(int $bin): object
    {
        $url = sprintf(self::BASE_URI, $bin);

        $binInfo = json_decode((string) $this->makeApiRequest($url), false);

        if (!is_object($binInfo)) {
            throw new BinListException($url . ' is not reachable or too many requests');
        }

        return $binInfo;
    }

    /**
     * @inheritDoc
     */
    public function makeApiRequest(string $url): string|false
    {
        return ENV_DEV
            ? file_get_contents(ROOT_PATH . '/data/binList.json')
            : file_get_contents($url); // TODO: replace with Guzzle Http Client
    }

}