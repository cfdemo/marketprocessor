<?php

namespace CurrencyFair\tests;

use Silex\Application;
use Silex\WebTestCase;
use CurrencyFair\Service\Provider\TotalsDataServiceProvider;

class TotalsDataServiceProviderTest extends WebTestCase
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
        $app['config.totalsDataExpiryPeriod'] = 7;


        return $app;
    }

    /**
     * @covers CurrencyFair\Service\Provider\TotalsDataServiceProvider::register
     * @covers CurrencyFair\Service\Provider\TotalsDataServiceProvider::boot
     */
    public function testRegisteringTotalsDataServiceProvider()
    {
        $this->app['predis'] = $this->getMock('Predis\\Client');

        $provider = new TotalsDataServiceProvider();
        $this->app->register($provider);
        $provider->boot($this->app);

        $this->assertArrayHasKey('totalsData', $this->app);
        $this->assertInstanceOf('CurrencyFair\Service\TotalsDataService', $this->app['totalsData']);
    }
}
