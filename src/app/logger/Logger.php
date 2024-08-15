<?php

declare(strict_types=1);

namespace App\app\logger;

use App\app\traits\SingletonTrait;

class Logger
{
    use SingletonTrait;

    private string $logFile = 'logs/app.log';

    public function log(string $message, string $level = 'ERROR'): void
    {
        $timestamp  = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] [$level] $message" . PHP_EOL;

        file_put_contents($this->logFile, $logMessage, FILE_APPEND);
    }
}
