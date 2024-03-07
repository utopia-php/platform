<?php

require_once __DIR__.'/../../vendor/autoload.php';

use Utopia\App;
use Utopia\Http\Adapter\Swoole\Request;
use Utopia\Http\Adapter\Swoole\Response;
use Utopia\Http\Adapter\Swoole\Server;
use Utopia\Http\Http;
use Utopia\Tests\TestPlatform;

ini_set('memory_limit', '512M');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('display_socket_timeout', -1);
error_reporting(E_ALL);

$platform = new TestPlatform();
$platform->init('http');

$app = new Http(new Server('0.0.0.0', 9999), 'UTC');
$app->start();
