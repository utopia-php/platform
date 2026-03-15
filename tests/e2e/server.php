<?php

require_once __DIR__.'/../../vendor/autoload.php';

use Utopia\DI\Container;
use Utopia\Http\Adapter\FPM\Server;
use Utopia\Http\Http;
use Utopia\Tests\TestPlatform;

ini_set('memory_limit', '512M');
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
ini_set('display_socket_timeout', '-1');
error_reporting(E_ALL);

$platform = new TestPlatform();
$platform->init('http');

$server = new Server(new Container());
$http = new Http($server, 'UTC');
$http->start();
