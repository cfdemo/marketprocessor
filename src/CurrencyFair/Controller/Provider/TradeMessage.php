<?php

namespace CurrencyFair\Controller\Provider;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TradeMessage implements ControllerProviderInterface
{
    /**
     * @param Application $app
     * @return ControllerCollection
     */
    public function connect(Application $app)
    {
        // returns a new Silex\ControllerCollection which holds our routing collection
        $tradeMessage = $app["controllers_factory"];

        $tradeMessage->post("/", "CurrencyFair\\Controller\\TradeMessageController::store")
            ->before(
                function (Request $request) {
                    if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
                        $data = json_decode($request->getContent(), true);
                        $request->request->replace(is_array($data) ? $data : array());
                    } else {
                        return new Response('', Response::HTTP_UNSUPPORTED_MEDIA_TYPE);
                    }
                }
            );

        $tradeMessage->get("/recent", "CurrencyFair\\Controller\\TradeMessageController::showRecent");

        return $tradeMessage;
    }
}
