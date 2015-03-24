<?php
/**
 * Created by PhpStorm.
 * User: Gareth
 * Date: 18/03/2015
 * Time: 21:13
 */

namespace CurrencyFair\Tests;

use CurrencyFair\Entity\TradeMessage;
use Symfony\Component\HttpFoundation\Request;
use Silex\WebTestCase;


class TradeMessageEntityTest extends WebTestCase {

    protected $tradeMessage;

    public function setUp()
    {
        parent::setUp();
        $this->tradeMessage = new TradeMessage();
    }

    public function createApplication()
    {
        $app = require __DIR__ . '/../../src/app.php';
        $app['debug'] = true;
        $app['exception_handler']->disable();

        return $app;
    }

    /**
     * @covers CurrencyFair\Entity\TradeMessage::createFromRequest
     */
    public function testObjectIsCreatedFromValidRequest()
    {
        $request = Request::create(
            '/trademessage',
            'POST',
            array(
                'userId' => '123456',
                'currencyFrom' => 'EUR',
                'currencyTo' => 'GBP',
                'amountSell' => 1000,
                'amountBuy' => 756.20,
                'rate' => 0.7562,
                'timePlaced' => '24-MAR-15 10:27:44',
                'originatingCountry' => 'DE'
                )
        );
        $this->tradeMessage->createFromRequest($request);

    }
}
