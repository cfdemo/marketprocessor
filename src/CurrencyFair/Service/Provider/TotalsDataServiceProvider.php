<?php

namespace CurrencyFair\Service\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use CurrencyFair\Service\TotalsDataService;

class TotalsDataServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(Application $app)
    {
        // Define services here
        $app['totalsData'] = $app->share(function () use ($app) {
            return new TotalsDataService($app);
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