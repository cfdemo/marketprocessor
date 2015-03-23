<?php

namespace CurrencyFair\Service;

use Silex\Application;

/**
 * Class TotalsDataService
 * Provides a service relating to Totals data
 * @package CurrencyFair\Service
 */
class TotalsDataService
{
    /**
     * @var int
     */
    protected $expiryPeriod;

    /**
     * @var
     */
    protected $cacheService;

    /**
     * Class constructor
     * @param Application $app
     * @param int|null $expiryPeriod Number of days
     */
    public function __construct(Application $app, $expiryPeriod = null)
    {
        $this->setCacheService($app['predis']);

        if (is_null($expiryPeriod)) {
            $expiryPeriod = $app['config.totalsDataExpiryPeriod'];
        }
        $this->setExpiryPeriod($expiryPeriod);
    }

    /**
     * Saves and increments the daily total for the currency pair in the cache
     * @param float $amountSell
     * @param $currencyFrom
     * @param null $currencyTo
     * @return float
     */
    public function save($amountSell, $currencyFrom, $currencyTo = null)
    {
        $dailyTotalAmountSellKey = $this->getCacheKey($currencyFrom, $currencyTo);

        $keyExists = $this->cacheService->exists($dailyTotalAmountSellKey);
        $result = $this->cacheService->incrByFloat(
            $dailyTotalAmountSellKey,
            $amountSell
        );

        if (!$keyExists) {
            // if key didn't originally exist, ensure it expires after defined expiry period
            $this->cacheService->expireAt($dailyTotalAmountSellKey, $this->generateExpiryTimestamp());
        }

        return $result;
    }

    /**
     * Returns the cache key to use
     * @param string $currencyFrom ISO
     * @param string $currencyTo ISO
     * @return string The key for cache lookup
     */
    public function getCacheKey($currencyFrom, $currencyTo = null)
    {
        $nowDateTime = new \DateTimeImmutable();
        $dailyTotalAmountSellKey = [];
        $dailyTotalAmountSellKey[] = "total";
        $dailyTotalAmountSellKey[] = $nowDateTime->format('Y-m-d');
        $dailyTotalAmountSellKey[] = $currencyFrom;
        if (!is_null($currencyTo)) {
            $dailyTotalAmountSellKey[] = $currencyTo;
        }
        $dailyTotalAmountSellKey = join(":", $dailyTotalAmountSellKey);

        return $dailyTotalAmountSellKey;
    }

    /**
     * @return int
     */
    public function getExpiryPeriod()
    {
        return $this->expiryPeriod;
    }

    /**
     * @param int $expiryPeriod
     */
    public function setExpiryPeriod($expiryPeriod)
    {
        $this->expiryPeriod = $expiryPeriod;
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
     * Generates the expiry timestamp using the $expiryPeriod class member
     * @return \DateTime
     */
    public function generateExpiryTimestamp()
    {
        $expiryDateTime = new \DateTime();
        $expiryDateTime->setTime(23, 59, 59);
        $expiryDateTime->add(new \DateInterval('P' . $this->getExpiryPeriod() . 'D'));

        return $expiryDateTime->format('U');
    }

}