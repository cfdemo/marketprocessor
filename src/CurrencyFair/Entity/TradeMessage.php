<?php

namespace CurrencyFair\Entity;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ExecutionContextInterface;

class TradeMessage
{
    /**
     * @var integer
     */
    public $userId;

    /**
     * @var string
     */
    public $currencyFrom;

    /**
     * @var string
     */
    public $currencyTo;

    /**
     * @var float
     */
    public $amountSell;

    /**
     * @var float
     */
    public $amountBuy;

    /**
     * @var float
     */
    public $rate;

    /**
     * @var string
     */
    public $timePlaced;

    /**
     * @var string
     */
    public $originatingCountry;

    /**
     * Constructor
     */
    public function __construct()
    {
    }

    /**
     * Creates a model use a Request object
     * @param Request $request Request to create the model from
     * @return $this
     */
    public function createFromRequest(Request $request)
    {
        $this->setUserId($request->request->get('userId'));
        $this->setCurrencyFrom($request->request->get('currencyFrom'));
        $this->setCurrencyTo($request->request->get('currencyTo'));
        $this->setAmountSell($request->request->get('amountSell'));
        $this->setAmountBuy($request->request->get('amountBuy'));
        $this->setRate($request->request->get('rate'));
        $this->setTimePlaced($request->request->get('timePlaced'));

        // ISO 639-1 standard is lower case, allows Locale assertion to work out of the box
        $this->setOriginatingCountry(strtolower($request->request->get('originatingCountry')));

        return $this;
    }

    /**
     * Load class constraints
     * @param ClassMetadata $metadata
     */
    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addGetterConstraint('userId', new Assert\NotBlank());
        $metadata->addGetterConstraint('currencyFrom', new Assert\NotBlank());
        $metadata->addGetterConstraint('currencyFrom', new Assert\Currency());
        $metadata->addGetterConstraint('currencyTo', new Assert\Currency());
        $metadata->addConstraint(new Assert\Callback('validateCurrenciesDiffer'));
        $metadata->addGetterConstraint('amountSell', new Assert\NotBlank());
        $metadata->addGetterConstraint('amountSell', new Assert\GreaterThan(['value' => 0]));
        $metadata->addGetterConstraint('amountBuy', new Assert\NotBlank());
        $metadata->addGetterConstraint('amountBuy', new Assert\GreaterThan(['value' => 0]));
        $metadata->addGetterConstraint('rate', new Assert\NotBlank());
        $metadata->addGetterConstraint('rate', new Assert\GreaterThan(['value' => 0]));
        $metadata->addGetterConstraint('timePlaced', new Assert\NotBlank());
        $metadata->addGetterConstraint('timePlaced', new Assert\Length(['min' => 18, 'max' => 18]));
        $metadata->addGetterConstraint('originatingCountry', new Assert\NotBlank());
        $metadata->addGetterConstraint('originatingCountry', new Assert\Locale());
    }

    /**
     * Callback method to validate currencies are different
     * @param $object
     * @param ExecutionContextInterface $context
     */
    public static function validateCurrenciesDiffer($object, ExecutionContextInterface $context)
    {
        if (strtolower($object->getCurrencyFrom()) == strtolower($object->getCurrencyTo())) {
            $context->addViolationAt('currencyFrom', 'Currencies cannot be identical!');
            $context->addViolationAt('currencyTo', 'Currencies cannot be identical!');
        }
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return string
     */
    public function getCurrencyFrom()
    {
        return $this->currencyFrom;
    }

    /**
     * @param string $currencyFrom
     */
    public function setCurrencyFrom($currencyFrom)
    {
        $this->currencyFrom = $currencyFrom;
    }

    /**
     * @return string
     */
    public function getCurrencyTo()
    {
        return $this->currencyTo;
    }

    /**
     * @param string $currencyTo
     */
    public function setCurrencyTo($currencyTo)
    {
        $this->currencyTo = $currencyTo;
    }

    /**
     * @return float
     */
    public function getAmountSell()
    {
        return $this->amountSell;
    }

    /**
     * @param float $amountSell
     */
    public function setAmountSell($amountSell)
    {
        $this->amountSell = $amountSell;
    }

    /**
     * @return float
     */
    public function getAmountBuy()
    {
        return $this->amountBuy;
    }

    /**
     * @param float $amountBuy
     */
    public function setAmountBuy($amountBuy)
    {
        $this->amountBuy = $amountBuy;
    }

    /**
     * @return float
     */
    public function getRate()
    {
        return $this->rate;
    }

    /**
     * @param float $rate
     */
    public function setRate($rate)
    {
        $this->rate = $rate;
    }

    /**
     * @return string
     */
    public function getTimePlaced()
    {
        return $this->timePlaced;
    }

    /**
     * @param string $timePlaced
     */
    public function setTimePlaced($timePlaced)
    {
        $this->timePlaced = $timePlaced;
    }

    /**
     * @return string
     */
    public function getOriginatingCountry()
    {
        return $this->originatingCountry;
    }

    /**
     * @param string $originatingCountry
     */
    public function setOriginatingCountry($originatingCountry)
    {
        $this->originatingCountry = $originatingCountry;
    }

    public function getJsonData()
    {

    }

}