<?php

use Silex\Application;

$app = new Application();

require_once __DIR__ . '/../app/bootstrap.php';
require_once __DIR__ . '/../app/routes.php';

return $app;
