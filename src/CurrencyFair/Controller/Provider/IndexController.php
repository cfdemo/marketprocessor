<?php

namespace CurrencyFair\Controller\Provider;

use Silex\Application;
use Silex\ControllerProviderInterface;

class IndexController implements ControllerProviderInterface
{
    /**
     * @param Application $app
     * @return ControllerCollection
     */
    public function connect(Application $app)
    {
        // returns a new Silex\ControllerCollection which holds our routing collection
        $index = $app["controllers_factory"];

        $index->get("/", "CurrencyFair\\Controller\\IndexController::index");

        return $index;
    }
}
