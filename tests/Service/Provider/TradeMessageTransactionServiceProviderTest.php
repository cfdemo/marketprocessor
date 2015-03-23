<?php

namespace CurrencyFair\tests;

use Silex\Application;
use Silex\WebTestCase;
use CurrencyFair\Service\Provider\TradeMessageTransactionServiceProvider;

class TradeMessageTransactionServiceProviderTest extends WebTestCase
{

    public function setUp()
    {
        parent::setUp();
    }

    public function createApplication()
    {
        $app = require __DIR__ . '/../../../src/app.php';
        $app['debug'] = true;
        $app['exception_handler']->disable();

        return $app;
    }

    /**
     * @covers CurrencyFair\Service\Provider\TradeMessageTransactionServiceProvider::register
     * @covers CurrencyFair\Service\Provider\TradeMessageTransactionServiceProvider::boot
     */
    public function testRegisteringTradeMessageTransactionServiceProvider()
    {
        $provider = new TradeMessageTransactionServiceProvider();
        $this->app->register($provider);
        $provider->boot($this->app);

        $this->assertArrayHasKey('tm_transaction', $this->app);
        $this->assertInstanceOf('CurrencyFair\Service\TradeMessageTransactionService', $this->app['tm_transaction']);
    }
}
