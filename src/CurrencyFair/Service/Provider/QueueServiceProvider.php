<?php

namespace CurrencyFair\Service\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use CurrencyFair\Service\QueueService;

class QueueServiceProvider implements ServiceProviderInterface
{

    /**
     * {@inheritdoc}
     */
    public function register(Application $app)
    {
        // Define services here
        $app['queueProvider'] = $app->share(function () use ($app) {
            return new QueueService($app);
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