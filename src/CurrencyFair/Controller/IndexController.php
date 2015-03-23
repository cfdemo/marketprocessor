<?php

namespace CurrencyFair\Controller;

use Silex\Application;

class IndexController
{

    /**
     * @param Application $app
     * @return string
     */
    public function index(Application $app)
    {
        return $app["twig"]->render("index.html.twig");
    }

}
