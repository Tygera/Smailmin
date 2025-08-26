<?php
declare(strict_types=1);

ini_set("display_errors", 1);
error_reporting(-1);

$config = require_once __DIR__ . '/../config/config.php';
$routes = require_once __DIR__ . '/../config/routes.php';

require_once __DIR__ . '/../vendor/autoload.php';

use Smailmin\Core\App;

$app = new App($config, $routes);

$app->run();
