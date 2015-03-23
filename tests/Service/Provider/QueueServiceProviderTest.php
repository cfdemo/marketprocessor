<?php

namespace CurrencyFair\tests;

use CurrencyFair\Service\Provider\QueueServiceProvider;
use Silex\Application;
use Silex\WebTestCase;


class QueueServiceProviderTest extends WebTestCase
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
     * @covers CurrencyFair\Service\Provider\QueueServiceProvider::register
     * @covers CurrencyFair\Service\Provider\QueueServiceProvider::boot
     */
    public function testRegisteringQueueServiceProvider()
    {
        $provider = new QueueServiceProvider();
        $this->app->register($provider);
        $provider->boot($this->app);

        $this->assertArrayHasKey('queueProvider', $this->app);
        $this->assertInstanceOf('CurrencyFair\Service\QueueService', $this->app['queueProvider']);
    }
}
