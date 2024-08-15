<?php

use App\app\Application;
use App\app\FileReader;
use App\app\http\BinListApiClient;
use App\app\http\ExchangeApiClient;
use PHPUnit\Framework\TestCase;

class CommissionsTest extends TestCase
{
    private array $configs = [];
    private array $rates   = [];

    private object $binInfo;

    protected function setUp(): void
    {
        parent::setUp();

        $this->configs = require ROOT_PATH . '/configs/config.php';
        $this->rates   = json_decode(file_get_contents(ROOT_PATH . $this->configs['ratesFilePath']), true);;
        $this->binInfo = json_decode(file_get_contents(ROOT_PATH . $this->configs['binListFilePath']), false);;
    }

    public function testCommissionsAreNotEmpty(): void
    {
        $exchangeRateClientMock = $this->getMockBuilder(ExchangeApiClient::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getExchangeRates'])
            ->getMock();
        $exchangeRateClientMock->method('getExchangeRates')->willReturn($this->rates);

        $binListClientMock = $this->getMockBuilder(BinListApiClient::class)->onlyMethods(['getBinInfo'])->getMock();
        $binListClientMock->method('getBinInfo')->willReturn($this->binInfo);

        $application = new Application($this->configs, $exchangeRateClientMock, $binListClientMock);
        $result = $application->run();

        $this->assertNotEmpty($result, 'Result array must contain data');
    }

    public function testFileLinesCountMatchCommissionCount(): void
    {
        $exchangeRateClientMock = $this->getMockBuilder(ExchangeApiClient::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getExchangeRates'])
            ->getMock();
        $exchangeRateClientMock->method('getExchangeRates')->willReturn($this->rates);

        $binListClientMock = $this->getMockBuilder(BinListApiClient::class)->onlyMethods(['getBinInfo'])->getMock();
        $binListClientMock->method('getBinInfo')->willReturn($this->binInfo);

        $application = new Application($this->configs, $exchangeRateClientMock, $binListClientMock);
        $result = $application->run();

        $file = new FileReader(ROOT_PATH . $this->configs['inputFilePath']);
        $fileLinesCount = $file->count();

        $this->assertCount($fileLinesCount, $result, 'Commissions count should match the lines count from the input file');
    }

    public function testInvalidInputFilePathTriggersTheException(): void
    {
        $this->configs['inputFilePath'] = 'invalidFilePath';

        $this->expectException(RuntimeException::class);

        (new Application($this->configs))->run();
    }
}