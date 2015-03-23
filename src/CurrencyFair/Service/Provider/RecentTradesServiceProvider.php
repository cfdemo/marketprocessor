<?php

namespace CurrencyFair\Service\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use CurrencyFair\Service\RecentTradesService;

class RecentTradesServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(Application $app)
    {
        // Define services here
        $app['recentTrades'] = $app->share(function () use ($app) {
            return new RecentTradesService($app);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function boot(Application $app)
    {
        // Configure the application
    }
}