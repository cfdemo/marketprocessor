<?php

namespace CurrencyFair\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Constraints as Assert;

class TradeMessageController
{

    /**
     * Shows recent trade messages
     * @param Application $app
     * @param Request $request
     * @return JsonResponse Containing recent confirmed trades
     */
    public function showRecent(Application $app, Request $request)
    {
        // The Key of the list
        $list = "recentTrades:" . $request->get('currencyFrom') . ":" . $request->get('currencyTo');

        $recentTradeMessages = $app['predis']->lrange($list, 0, $app['config.numberOfRecentTrades'] - 1);

        $jsonDecodedMessages = [];
        if (count($recentTradeMessages)) {
            foreach ($recentTradeMessages as $message) {
                $jsonDecodedMessages[] = json_decode($message);
            }
        }

        return new JsonResponse($jsonDecodedMessages, Response::HTTP_OK);
    }

    /**
     * Processes the trade message and stores confirmed trade.
     * Stores captured metrics. Sends messages to queue
     * @param Application $app
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function store(Application $app, Request $request)
    {
        // create a new trade message, using POST data
        $tradeMessageEntity = $app['tradeMessageEntity']->createFromRequest($request);

        // Validate data
        $errors = $app['validator']->validate($tradeMessageEntity);
        if (count($errors) > 0) {
            $validationErrors = "";
            foreach ($errors as $error) {
                $validationErrors .= $error->getPropertyPath() . ' ' . $error->getMessage() . "\n";
            }

            return new Response($validationErrors, Response::HTTP_BAD_REQUEST);
        }

        // Actual real-time trade transaction calls would be performed here
        // They could fail if the rate has changed/unavailable for example, assuming true for demo
        $tradeMessageTransactionResult = $app['tm_transaction']->process($tradeMessageEntity);

        if ($tradeMessageTransactionResult->isConfirmed() === true) {

            // Store the trade in a list of recent transactions
            $app['recentTrades']->save($tradeMessageTransactionResult->getData());

            // Increment the daily Sell Amount total for the currencyFrom
            $dailyCurrencyFromTotal = $app['totalsData']->save(
                (float)$tradeMessageTransactionResult->getData()['amountSell'],
                $tradeMessageTransactionResult->getData()['currencyFrom']);

            // Increment the daily Sell Amount total for the currency pair
            $dailyCurrencyPairTotal = $app['totalsData']->save(
                (float)$tradeMessageTransactionResult->getData()['amountSell'],
                $tradeMessageTransactionResult->getData()['currencyFrom'],
                $tradeMessageTransactionResult->getData()['currencyTo']);

            $dailyTotalsMessages = [
                'topic' => $app['totalsData']->getCacheKey($tradeMessageTransactionResult->getData()['currencyFrom']),
                'currencyPair' => (float)$dailyCurrencyFromTotal,
                'currencySingle' => (float)$dailyCurrencyPairTotal,

            ];

            // Send order to Queue for real time subscribers
            $topic = 'indvTrade:' . $tradeMessageTransactionResult->getData()['currencyFrom'] . ":" . $tradeMessageTransactionResult->getData()['currencyTo'];
            $tradeMessage = array_merge($tradeMessageTransactionResult->getData(), ['topic' => $topic]);
            $jsonEncodedOrder = json_encode($tradeMessage);
            if (!$app['queueProvider']->send($jsonEncodedOrder)) {
                $app['monolog']->addError('Failed to send trade to Queue');
            }

            // Send daily totals data to queue for real time subscribers
            $jsonEncodedDailyTotals = json_encode($dailyTotalsMessages);
            if (!$app['queueProvider']->send($jsonEncodedDailyTotals)) {
                $app['monolog']->addError('Failed to send daily totals to Queue');
            }

            return new JsonResponse($tradeMessageTransactionResult->getData(), Response::HTTP_OK);
        }

        return new JsonResponse(["statusCode" => Response::HTTP_BAD_REQUEST, "message" => 'Unable to process trade'],
            Response::HTTP_BAD_REQUEST);

    }

}