<?php

use CurrencyFair\Controller\Provider\TradeMessage;
use CurrencyFair\Controller\Provider\IndexController;

$app->mount('/', new IndexController());
$app->mount('/trademessage', new TradeMessage());
