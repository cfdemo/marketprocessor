<?php

namespace CurrencyFair\Service\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use CurrencyFair\Service\TradeMessageTransactionService;

class TradeMessageTransactionServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(Application $app)
    {
        // Define services here
        $app['tm_transaction'] = $app->share(function () use ($app) {
            return new TradeMessageTransactionService();
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