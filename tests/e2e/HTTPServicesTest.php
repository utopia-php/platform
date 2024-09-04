<?php

namespace Utopia\Tests;

use PHPUnit\Framework\TestCase;
use Utopia\DI\Container;
use Utopia\DI\Dependency;
use Utopia\Http\Adapter\FPM\Request;
use Utopia\Http\Adapter\FPM\Response;
use Utopia\Http\Adapter\FPM\Server;
use Utopia\Http\Http;

class HttpServicesTest extends TestCase
{
    protected ?string $method;

    protected ?string $uri;

    protected ?Http $http;

    public function setUp(): void
    {
        Http::reset();
        $platform = new TestPlatform();
        $platform->init('http');

        $server = new Server();
        $this->http = new Http($server, new Container(), 'UTC');
        $this->http->setRequestClass(Request::class);
        $this->http->setResponseClass(Response::class);
    }

    public function tearDown(): void
    {
        $this->http = null;
    }

    public function testRootAction()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/';


        $context = new Container();
        $context->set( (new Dependency())->setName('response')->setCallback(fn () =>  new MockResponse()));
        $context->set( (new Dependency())->setName('request')->setCallback(fn () => new Request()));

        \ob_start();
        $this->http->run($context);
        $response = \ob_get_contents();
        \ob_end_clean();

        $this->assertEquals('Hello World!', $response);
    }

    public function testChunkedAction()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/chunked';

        $res = new MockResponse();
        $context = new Container();
        $context->set( (new Dependency())->setName('response')->setCallback(fn () =>  $res));
        $context->set( (new Dependency())->setName('request')->setCallback(fn () => new Request()));

        \ob_start();
        $this->http->run($context);
        $response = \ob_get_contents();
        \ob_end_clean();

        $this->assertEquals('Hello World!', $response);
    }

    public function testRedirectAction()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/redirect';

        $res = new MockResponse();
        $context = new Container();
        $context->set( (new Dependency())->setName('response')->setCallback(fn () =>  $res));
        $context->set( (new Dependency())->setName('request')->setCallback(fn () => new Request()));

        $this->http->run($context);

        $this->assertEquals('/', $res->getHeaders()['Location']);
    }

    public function testHook()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/';

        $res = new MockResponse();
        $context = new Container();
        $context->set( (new Dependency())->setName('response')->setCallback(fn () =>  $res));
        $context->set( (new Dependency())->setName('request')->setCallback(fn () => new Request()));

        \ob_start();
        $this->http->run($context);
        $response = \ob_get_contents();
        \ob_end_clean();

        $this->assertEquals('Hello World!', $response);
        $this->assertEquals('init-called', $res->getHeaders()['x-init']);

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/chunked';

        $res1 = new MockResponse();
        $context = new Container();
        $context->set( (new Dependency())->setName('response')->setCallback(fn () =>  $res1));
        $context->set( (new Dependency())->setName('request')->setCallback(fn () => new Request()));

        \ob_start();
        $this->http->run($context);
        $response = \ob_get_contents();
        \ob_end_clean();

        $this->assertEquals('Hello World!', $response);
        $this->assertEquals('', ($res1->getHeaders()['x-init'] ?? ''));
    }
}
