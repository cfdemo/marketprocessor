<?php

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Symfony\Component\HttpFoundation\JsonResponse;
use Igorw\Silex\ConfigServiceProvider;
use CurrencyFair\Service\Provider\TradeMessageTransactionServiceProvider;
use CurrencyFair\Entity\TradeMessage;
use CurrencyFair\Service\Provider\RecentTradesServiceProvider;
use CurrencyFair\Service\Provider\TotalsDataServiceProvider;
use CurrencyFair\Service\Provider\QueueServiceProvider;

// handling CORS preflight request
$app->before(function (Request $request) {
    if ($request->getMethod() === "OPTIONS") {
        $response = new Response();
        $response->headers->set("Access-Control-Allow-Origin", "*");  // promiscuous for demo
        $response->headers->set("Access-Control-Allow-Methods", "GET,POST,PUT,DELETE,OPTIONS");
        $response->headers->set("Access-Control-Allow-Headers", "Content-Type");
        $response->setStatusCode(200);

        return $response->send();
    }
}, Application::EARLY_EVENT);

// handling CORS response with right headers
$app->after(function (Request $request, Response $response) {
    $response->headers->set("Access-Control-Allow-Origin", "*"); // promiscuous for demo
    $response->headers->set("Access-Control-Allow-Methods", "GET,POST,PUT,DELETE,OPTIONS");
});

$env = getenv('APP_ENV') ?: 'demo';
$app->register(new ConfigServiceProvider(__DIR__ . "/../config/config_$env.php"));
$app->register(new ValidatorServiceProvider());
$app->register(new ServiceControllerServiceProvider());
$app->register(new TwigServiceProvider());
$app->register(new Predis\Silex\ClientServiceProvider(), [
        $app['predis.parameters'],
        $app['predis.options']
    ]
);

// Custom Providers
$app->register(new TradeMessageTransactionServiceProvider());
$app->register(new RecentTradesServiceProvider());
$app->register(new TotalsDataServiceProvider());
$app->register(new QueueServiceProvider());
$app['tradeMessageEntity'] = function () {
    return new TradeMessage();
};

$app->error(function (\Exception $e, $code) use ($app) {
    $app['monolog']->addError($e->getMessage());
    $app['monolog']->addError($e->getTraceAsString());

    switch ($code) {
        case 404:
            $message = "The requested page could not be found";
            break;
        default:
            $message = "Sorry, something went wrong";
    }

    return new JsonResponse(array(
        "message" => $message,
    ));

});