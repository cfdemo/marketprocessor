<?php

namespace CurrencyFair\tests;

use Silex\Application;
use Silex\WebTestCase;
use CurrencyFair\Service\RecentTradesService;

class RecentTradesServiceTest extends WebTestCase
{

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
        $app['config.numberOfRecentTrades'] = 10;


        return $app;
    }

    /**
     * @covers CurrencyFair\Service\RecentTradesService::__construct
     * @covers CurrencyFair\Service\RecentTradesService::getNumberOfRecentTrades
     * @covers CurrencyFair\Service\RecentTradesService::setNumberOfRecentTrades
     * @covers CurrencyFair\Service\RecentTradesService::getCacheService
     * @covers CurrencyFair\Service\RecentTradesService::setCacheService
     */
    public function testCheckInstanceIsCreated()
    {
        $recentTradesService = new RecentTradesService($this->app, 14);
        $this->assertInstanceOf('CurrencyFair\Service\RecentTradesService', $recentTradesService);
        $this->assertInstanceOf('Predis\\Client', $recentTradesService->getCacheService());
        $this->assertEquals(14, $recentTradesService->getNumberOfRecentTrades());
    }

    /**
     * @covers CurrencyFair\Service\RecentTradesService::save
     * @dataProvider transactionResultDataProvider
     */
    public function testSavingRecentTrade($transactionResultData)
    {
        $key = "recentTrades:";
        $key .= $transactionResultData['currencyFrom'] . ":";
        $key .= $transactionResultData['currencyTo'];

        $stubPredis = $this->getMock('Predis\\Client', array('transaction','lpush', 'ltrim'));
        $stubPredis->expects($this->once())
            ->method('transaction')
            ->will($this->returnValue(true));
        $stubPredis->expects($this->any())
            ->method('lpush')
            ->with($key, $this->equalTo(json_encode($transactionResultData)))
            ->will($this->returnValue(true));
        $stubPredis->expects($this->any())
            ->method('ltrim')
            ->with($key, 0, 9)
            ->will($this->returnValue(true));
        $this->app['predis'] = $stubPredis;

        $recentTradesService = new RecentTradesService($this->app);
        $result = $recentTradesService->save($transactionResultData);
        $this->assertTrue($result);
    }

    public function transactionResultDataProvider()
    {
        return [
            [
                [
                    "idTransaction" => uniqid(),  // suitable just for mocking here
                    "currencyFrom" => 'EUR',
                    "currencyTo" => 'GBP',
                    "amountSell" => '754.34',
                    "amountBuy" => '345.56',
                    "originatingCountry" => 'DE',
                    'timestamp'=> '24-MAR-15 10:27:44'
                ]
            ]
        ];
    }
}
