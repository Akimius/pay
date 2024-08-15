<?php

declare(strict_types=1);

namespace App\app;

use App\app\calculator\CalculatorFactory;
use App\app\exceptions\BinListException;
use App\app\exceptions\ExchangeRateException;
use App\app\http\BinListApiClient;
use App\app\http\ExchangeApiClient;
use App\app\logger\Logger;
use InvalidArgumentException;
use LogicException;
use RuntimeException;
use Throwable;

class Application
{
    private array $configs;

    private ?ExchangeApiClient $exchangeRateClient;
    private ?BinListApiClient $binListClient;

    public function __construct(array $configs, ?ExchangeApiClient $exchangeRateClient = null, ?BinListApiClient $binListClient = null)
    {
        $this->configs            = $configs;
        $this->exchangeRateClient = $exchangeRateClient ?? new ExchangeApiClient($configs);
        $this->binListClient      = $binListClient ?? new BinListApiClient();
    }

    /**
     * @return array
     */
    public function run(): array
    {
        $commissions = [];

        $file = new FileReader(ROOT_PATH . $this->configs['inputFilePath']);

        try {
            $this->validateFileNotEmpty($file);

            $rates = $this->exchangeRateClient->getExchangeRates(); // TODO: cache rates in Redis to save money on API calls

            foreach ($file as $line) {
                $row = json_decode(trim($line), true);

                if (!is_array($row) || !isset($row['bin'], $row['amount'], $row['currency'])) {
                    $errorMessage = "Error decoding JSON on line: " . $file->getLineNumber();
                    echo $errorMessage . PHP_EOL;
                    Logger::getInstance()->log($errorMessage);

                    continue; // TODO: this logic will depend on requirements: skip (continue) just one invalid line of the input file or throw an exception to stop executing the whole script
                }

                $binInfo       = $this->binListClient->getBinInfo((int) $row['bin']);
                $issuerCountry = $binInfo->country->alpha2 ?? null;
                if (null === $issuerCountry) {
                    continue; // TODO: this logic will depend on requirements: skip (continue) just one invalid line of the input file or throw an exception to stop executing the whole script
                }

                $rate = $rates['rates'][$row['currency']] ?? null;
                if (null === $rate) {
                    throw new LogicException('Currency not found: ' . $row['currency']);
                }

                $commissionCalculator = CalculatorFactory::make($issuerCountry);
                $commissions[] = $commissionCalculator->calculate((float) $row['amount'], $rate);
            }
        } catch (ExchangeRateException $exception) {
            $this->logMessage("Exchange Rates API failed to respond: " . $exception->getMessage());
        } catch (BinListException $exception) {
            $this->logMessage("BIN List API failed to respond: " . $exception->getMessage());
        } catch (RuntimeException $exception) {
            $this->logMessage("Error opening the file: " . $exception->getMessage());
        } catch (LogicException $exception) {
            $this->logMessage("Currency rates are incomplete or corrupted: " . $exception->getMessage());
        } catch (Throwable $exception) {
            $this->logMessage("Something went wrong: " . $exception->getMessage());
        }

        return $commissions;
    }

    /**
     * @param string $message
     * @param string $level
     * @return void
     */
    private function logMessage(string $message, string $level = 'ERROR'): void
    {
        echo $message . PHP_EOL;
        Logger::getInstance()->log($message, $level);
    }

    /**
     * @param FileReader $file
     */
    private function validateFileNotEmpty(FileReader $file): void
    {
        if ($file->count() > 0) {
            return;
        }

        throw new InvalidArgumentException('Input File is empty!');
    }
}
