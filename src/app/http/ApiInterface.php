<?php

declare(strict_types=1);

namespace App\app\http;

interface ApiInterface
{
    /**
     * @param string $url
     * @return false|string
     */
    public function makeApiRequest(string $url): string|false;
}