<?php

namespace CurrencyFair\Service;

use Silex\Application;

/**
 * Class QueueService
 * Provides a queue service for messaging
 * @package CurrencyFair\Service
 */
class QueueService
{

    protected $context;

    public $app;

    /**
     * Class constructor
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->context = new \ZMQContext();
    }

    /**
     * Opens a socket and sends a message to the queue
     * @param string $message
     * @return bool
     * @internal param array $transactionResult
     */
    public function send($message)
    {
        $socket = $this->context->getSocket(\ZMQ::SOCKET_PUSH, $this->app['config.zeromq.persistent_id']);
        $socket->connect($this->app['config.zeromq.dsn']);

        try {
            $this->app['monolog']->addInfo('Sending message to queue');
            $socket->send($message);
        } catch (ZMQSocketException $e) {
            $this->app['monolog']->addError($e->getMessage());

            return false;
        }

        return true;
    }

}