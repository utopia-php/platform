<?php

require_once __DIR__.'/../../vendor/autoload.php';

use Utopia\App;
use Utopia\Request;
use Utopia\Response;
use Utopia\Tests\TestPlatform;

ini_set('memory_limit', '512M');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('display_socket_timeout', -1);
error_reporting(E_ALL);

$platform = new TestPlatform();
$platform->init('http');

$request = new Request();
$response = new Response();

$app = new App('UTC');
$app->run($request, $response);
