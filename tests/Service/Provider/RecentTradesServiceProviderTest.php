<?php

namespace CurrencyFair\tests;

use Silex\Application;
use Silex\WebTestCase;
use CurrencyFair\Service\Provider\RecentTradesServiceProvider;
use Predis\Client;

class RecentTradesServiceProviderTest extends WebTestCase
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
        $app['predis'] = $this->getMock('Predis\\Client');
        $app['config.numberOfRecentTrades'] = 10;


        return $app;
    }

    /**
     * @covers CurrencyFair\Service\Provider\RecentTradesServiceProvider::register
     */
    public function testRegisteringRecentTradesServiceProvider()
    {
        $provider = new RecentTradesServiceProvider($this->app);
        $this->app->register($provider);
        $provider->boot($this->app);

        $this->assertArrayHasKey('recentTrades', $this->app);
        $this->assertInstanceOf('CurrencyFair\Service\RecentTradesService', $this->app['recentTrades']);
    }
}
