<?php

namespace CurrencyFair\Service;

use CurrencyFair\Entity\TradeMessage;

/**
 * Class TradeMessageTransactionService
 * Provides a service relating to Trade Message transactions.
 * Currently this class assumes all sent trade messages are confirmed
 * @package CurrencyFair\Service
 */
class TradeMessageTransactionService
{
    /**
     * @var array
     */
    private $data;

    /**
     * @var boolean
     */
    private $confirmed;

    /**
     * Class constructor
     */
    public function __construct()
    {
    }

    /**
     * Processes a Trade Message Transaction
     * @param TradeMessage $tradeMessage
     * @return $this
     */
    public function process(TradeMessage $tradeMessage)
    {
        // Transaction logic would occur here, assume trade was confirmed and that
        // rates hadn't changed etc etc
        $this->setConfirmed(true);

        $nowDateTime = new \DateTime();
        $this->setData([
            "idTransaction" => uniqid(),  // suitable just for mocking here
            "currencyFrom" => $tradeMessage->getCurrencyFrom(),
            "currencyTo" => $tradeMessage->getCurrencyTo(),
            "amountSell" => $tradeMessage->getAmountSell(),
            "amountBuy" => $tradeMessage->getAmountBuy(),
            "originatingCountry" => $tradeMessage->getOriginatingCountry(),
            'timestamp' => $nowDateTime->format(\DateTime::ISO8601)
        ]);

        return $this;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return boolean
     */
    public function isConfirmed()
    {
        return $this->confirmed;
    }

    /**
     * @param boolean $confirmed
     */
    public function setConfirmed($confirmed)
    {
        $this->confirmed = $confirmed;
    }


}