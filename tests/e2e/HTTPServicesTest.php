<?php

namespace Utopia\Tests;

use PHPUnit\Framework\TestCase;
use Utopia\App;
use Utopia\Request;

class HttpServicesTest extends TestCase
{
    protected ?App $app = null;

    protected ?string $method;

    protected ?string $uri;

    public function setUp(): void
    {
        App::reset();
        $platform = new TestPlatform();
        $platform->init('http');

        $this->app = new App('UTC');
    }

    public function tearDown(): void
    {
        $this->app = null;
    }

    public function testRootAction()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/';

        $res = new MockResponse();
        \ob_start();
        $this->app->run(new Request(), $res);
        $response = \ob_get_contents();
        \ob_end_clean();

        $this->assertEquals('Hello World!', $response);
    }

    public function testChunkedAction()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/chunked';

        \ob_start();
        $this->app->run(new Request(), new MockResponse());
        $response = \ob_get_contents();
        \ob_end_clean();

        $this->assertEquals('Hello World!', $response);
    }

    public function testRedirectAction()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/redirect';

        $res = new MockResponse();
        $this->app->run(new Request(), $res);

        $this->assertEquals('/', $res->getHeaders()['Location']);
    }

    public function testHook()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/';

        $res = new MockResponse();
        \ob_start();
        $this->app->run(new Request(), $res);
        $response = \ob_get_contents();
        \ob_end_clean();

        $this->assertEquals('Hello World!', $response);
        $this->assertEquals('init-called', $res->getHeaders()['x-init']);
        App::reset();

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/chunked';

        $res1 = new MockResponse();
        \ob_start();
        $this->app->run(new Request(), $res1);
        $response = \ob_get_contents();
        \ob_end_clean();

        $this->assertEquals('Hello World!', $response);
        $this->assertEquals('', ($res1->getHeaders()['x-init'] ?? ''));
    }
}
