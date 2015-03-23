<?php

namespace CurrencyFair\Service;

use Silex\Application;

/**
 * Class RecentTradesService
 * Provides a service relating to recent trades
 * @package CurrencyFair\Service
 */
class RecentTradesService
{
    /**
     * @var int
     */
    protected $numberOfRecentTrades;

    /**
     * @var
     */
    protected $cacheService;

    /**
     * @var string
     */
    protected $cacheKey;

    /**
     * Class constructor
     * @param Application $app
     * @param int|null $numberRecentTrades
     */
    public function __construct(Application $app, $numberRecentTrades = null)
    {
        if (is_null($numberRecentTrades)) {
            $numberRecentTrades = $app['config.numberOfRecentTrades'];
        }

        $this->setNumberOfRecentTrades($numberRecentTrades);
        $this->setCacheService($app['predis']);
    }

    /**
     * Saves a Trade Message Transaction in the cache
     * @param array $transactionResult
     * @return bool
     */
    public function save($transactionResult)
    {
        $this->setCacheKey($transactionResult);

        $responses = $this->getCacheService()->transaction(function ($tx) use ($transactionResult) {
            $tx->lpush($this->getCacheKey(), json_encode($transactionResult));
            $tx->ltrim($this->getCacheKey(), 0, ($this->getNumberOfRecentTrades() - 1));
        });

        return $responses;
    }

    /**
     * @return int
     */
    public function getNumberOfRecentTrades()
    {
        return $this->numberOfRecentTrades;
    }

    /**
     * @param int $numberOfRecentTrades
     */
    public function setNumberOfRecentTrades($numberOfRecentTrades)
    {
        $this->numberOfRecentTrades = $numberOfRecentTrades;
    }

    /**
     * @return mixed
     */
    public function getCacheService()
    {
        return $this->cacheService;
    }

    /**
     * @param mixed $cacheService
     */
    public function setCacheService($cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * @return mixed
     */
    public function getCacheKey()
    {
        return $this->cacheKey;
    }

    /**
     * @param array $data Containing 'currencyFrom' and 'currencyTo' keys
     */
    public function setCacheKey($data)
    {
        $cacheKey = "recentTrades:" . $data['currencyFrom'] . ":" . $data['currencyTo'];
        $this->cacheKey = $cacheKey;
    }


}