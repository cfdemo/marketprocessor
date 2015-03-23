<?php

namespace CurrencyFair\tests;

use Silex\Application;
use Silex\WebTestCase;
use CurrencyFair\Service\TotalsDataService;
use Predis\Client;

class TotalsDataServiceTest extends WebTestCase
{
    /**
     * @var TotalsDataService
     */
    protected $totalsDataService;

    public function setUp()
    {
        parent::setUp();
    }

    public function createApplication()
    {
        $app = require __DIR__ . '/../../src/app.php';
        $app['debug'] = true;
        $app['exception_handler']->disable();
        $app['predis'] = $this->getMock('Predis\\Client');
        $app['config.totalsDataExpiryPeriod'] = 7;

        return $app;
    }

    /**
     * @covers CurrencyFair\Service\TotalsDataService::__construct
     * @covers CurrencyFair\Service\TotalsDataService::setCacheService
     * @covers CurrencyFair\Service\TotalsDataService::getCacheService
     * @covers CurrencyFair\Service\TotalsDataService::setExpiryPeriod
     * @covers CurrencyFair\Service\TotalsDataService::getExpiryPeriod
     */
    public function testCheckInstanceIsCreated()
    {
        $totalsDataService = new TotalsDataService($this->app, 14);
        $this->assertInstanceOf('CurrencyFair\Service\TotalsDataService', $totalsDataService);
        $this->assertInstanceOf('Predis\\Client', $totalsDataService->getCacheService());
        $this->assertEquals(14, $totalsDataService->getExpiryPeriod());
    }

    /**
     * @covers CurrencyFair\Service\TotalsDataService::save
     * @dataProvider totalsDataBothCurrenciesProvider
     */
    public function testSavingTotalsDataWithBothCurrenciesWithNoExistingKey($amountSell, $currencyFrom, $currencyTo)
    {
        $key = $this->getKeyFormat($currencyFrom, $currencyTo);

        // Mock the required Redis commands
        $stubPredis = $this->createMockFunctionsForNoExistingKey($amountSell, $key);
        $this->app['predis'] = $stubPredis;

        $totalsDataService = new TotalsDataService($this->app);
        $result = $totalsDataService->save($amountSell, $currencyFrom, $currencyTo);

        $this->assertTrue($result);
    }

    /**
     * @covers CurrencyFair\Service\TotalsDataService::save
     * @dataProvider totalsDataBothCurrenciesProvider
     */
    public function testSavingTotalsDataWithBothCurrenciesWithExistingKey($amountSell, $currencyFrom, $currencyTo)
    {
        $key = $this->getKeyFormat($currencyFrom, $currencyTo);

        // Mock the required Redis commands
        $stubPredis = $this->createMockFunctionsForExistingKey($amountSell, $key);
        $this->app['predis'] = $stubPredis;

        $totalsDataService = new TotalsDataService($this->app);
        $result = $totalsDataService->save($amountSell, $currencyFrom, $currencyTo);

        $this->assertTrue($result);
    }

    /**
     * @covers CurrencyFair\Service\TotalsDataService::save
     * @dataProvider totalsDataSingleCurrenciesProvider
     */
    public function testSavingTotalsDataWithSingleCurrenciesWithNoExistingKey($amountSell, $currencyFrom)
    {
        $key = $this->getKeyFormat($currencyFrom);

        // Mock the required Redis commands
        $stubPredis = $this->createMockFunctionsForNoExistingKey($amountSell, $key);
        $this->app['predis'] = $stubPredis;

        $totalsDataService = new TotalsDataService($this->app);
        $result = $totalsDataService->save($amountSell, $currencyFrom);

        $this->assertTrue($result);
    }

    /**
     * @covers CurrencyFair\Service\TotalsDataService::save
     * @dataProvider totalsDataSingleCurrenciesProvider
     */
    public function testSavingTotalsDataWithSingleCurrenciesWithExistingKey($amountSell, $currencyFrom)
    {
        $key = $this->getKeyFormat($currencyFrom);

        // Mock the required Redis commands
        $stubPredis = $this->createMockFunctionsForExistingKey($amountSell, $key);
        $this->app['predis'] = $stubPredis;

        $totalsDataService = new TotalsDataService($this->app);
        $result = $totalsDataService->save($amountSell, $currencyFrom);

        $this->assertTrue($result);
    }


    /**
     * @covers CurrencyFair\Service\TotalsDataService::getCacheKey
     * @dataProvider totalsDataCurrencyPairProvider
     */
    public function testCacheKeyIsCorrectUsingCurrencyPair($currencyFrom, $currencyTo)
    {
        $nowDateTime = new \DateTimeImmutable();
        $expectedKey = "total:".$nowDateTime->format('Y-m-d').":$currencyFrom:$currencyTo";

        $totalsDataService = new TotalsDataService($this->app);
        $this->assertEquals($expectedKey, $totalsDataService->getCacheKey($currencyFrom, $currencyTo));
    }

    /**
     * @covers CurrencyFair\Service\TotalsDataService::getCacheKey
     * @dataProvider totalsDataSingleCurrencyProvider
     */
    public function testCacheKeyIsCorrectUsingSingleCurrency($currencyFrom)
    {
        $nowDateTime = new \DateTimeImmutable();
        $expectedKey = "total:".$nowDateTime->format('Y-m-d').":$currencyFrom";

        $totalsDataService = new TotalsDataService($this->app);
        $this->assertEquals($expectedKey, $totalsDataService->getCacheKey($currencyFrom));
    }

    /**
     * @dataProvider expiryTimestampProvider
     */
    public function testGenerationOfExpiryTimestampIsCorrect($expiryPeriod)
    {
        $expectedExpiryDateTime = new \DateTime();
        $expectedExpiryDateTime->setTime(23, 59, 59);
        $expectedExpiryDateTime->add(new \DateInterval('P' . $expiryPeriod . 'D'));

        $totalsDataService = new TotalsDataService($this->app, $expiryPeriod);
        $this->assertEquals($expectedExpiryDateTime->format('U'), $totalsDataService->generateExpiryTimestamp());
    }

    /**
     * Get the key format
     * @param $currencyFrom
     * @param $currencyTo
     * @return string
     * @internal param $stubPredis
     */
    protected function getKeyFormat($currencyFrom, $currencyTo = null)
    {
        $totalsDataService = new TotalsDataService($this->app);
        return $totalsDataService->getCacheKey($currencyFrom, $currencyTo);
    }

    /**
     * Get the key format
     * @return string expiry timestamp
     */
    protected function getExpiryDateFormat()
    {
        $totalsDataService = new TotalsDataService($this->app);
        return $totalsDataService->generateExpiryTimestamp();
    }

    public function totalsDataBothCurrenciesProvider()
    {
        return [
            [
                "amountSell" => '754.34',
                "currencyFrom" => 'EUR',
                "currencyTo" => 'GBP',
            ],
            [
                "amountSell" => '33',
                "currencyFrom" => 'USD',
                "currencyTo" => 'GBP',
            ],
        ];
    }

    public function totalsDataSingleCurrenciesProvider()
    {
        return [
            [
                "amountSell" => '754.34',
                "currencyFrom" => 'EUR'
            ],
            [
                "amountSell" => '33',
                "currencyFrom" => 'USD'
            ],
        ];
    }


    public function totalsDataCurrencyPairProvider()
    {
        return [
            ['EUR', 'GBP'],
            ['GBP', 'EUR'],
            ['USD', 'EUR']
        ];
    }

    public function totalsDataSingleCurrencyProvider()
    {
        return [
            ['EUR'],
            ['GBP'],
            ['USD']
        ];
    }

    public function expiryTimestampProvider()
    {
        return [
            [3, 8, 20, 50]
        ];
    }

    /**
     * Creates a mock Predis object for tests where a key does not exist
     * @param $amountSell
     * @param $key
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function createMockFunctionsForNoExistingKey($amountSell, $key)
    {
        $stubPredis = $this->createPredisMock();
        $stubPredis->expects($this->once())
            ->method('exists')
            ->will($this->returnValue(false));
        $stubPredis->expects($this->any())
            ->method('incrByFloat')
            ->with($key, $this->equalTo($amountSell))
            ->will($this->returnValue(true));
        $stubPredis->expects($this->any())
            ->method('expireAt')
            ->with($key, $this->getExpiryDateFormat())
            ->will($this->returnValue(true));

        return $stubPredis;
    }

    /**
     * Creates a mock Predis object for tests where a key already exists
     * @param $amountSell
     * @param $key
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function createMockFunctionsForExistingKey($amountSell, $key)
    {
        $stubPredis = $this->createPredisMock();
        $stubPredis->expects($this->once())
            ->method('exists')
            ->will($this->returnValue(true));
        $stubPredis->expects($this->any())
            ->method('incrByFloat')
            ->with($key, $this->equalTo($amountSell))
            ->will($this->returnValue(true));
        $stubPredis->expects($this->never())
            ->method('expireAt')
            ->with($key, $this->getExpiryDateFormat())
            ->will($this->returnValue(true));

        return $stubPredis;
    }

    /**
     * Creates a mock Predis object
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function createPredisMock()
    {
        $stubPredis = $this->getMock('Predis\\Client', array('exists', 'incrByFloat', 'expireAt'));
        return $stubPredis;
    }

}