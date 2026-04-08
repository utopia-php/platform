<?php

namespace Utopia\Tests;

use PHPUnit\Framework\TestCase;
use Utopia\DI\Container;
use Utopia\Http\Adapter\FPM\Request;
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

        $server = new Server(new Container());
        $this->http = new Http($server, 'UTC');
    }

    public function tearDown(): void
    {
        $this->http = null;
    }

    public function testRootAction()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/';

        $request = new Request();
        $response = new MockResponse();

        \ob_start();
        $this->http->run($request, $response);
        $result = \ob_get_contents();
        \ob_end_clean();

        $this->assertEquals('Hello World!', $result);
    }

    public function testChunkedAction()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/chunked';

        $request = new Request();
        $response = new MockResponse();

        \ob_start();
        $this->http->run($request, $response);
        $result = \ob_get_contents();
        \ob_end_clean();

        $this->assertEquals('Hello World!', $result);
    }

    public function testRedirectAction()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/redirect';

        $request = new Request();
        $response = new MockResponse();

        $this->http->run($request, $response);

        $this->assertEquals('/', $response->getHeaders()['Location']);
    }

    public function testHook()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/';

        $request = new Request();
        $response = new MockResponse();

        \ob_start();
        $this->http->run($request, $response);
        $result = \ob_get_contents();
        \ob_end_clean();

        $this->assertEquals('Hello World!', $result);
        $this->assertEquals('init-called', $response->getHeaders()['x-init']);

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/chunked';

        $request1 = new Request();
        $response1 = new MockResponse();

        \ob_start();
        $this->http->run($request1, $response1);
        $result = \ob_get_contents();
        \ob_end_clean();

        $this->assertEquals('Hello World!', $result);
        $this->assertEquals('', ($response1->getHeaders()['x-init'] ?? ''));
    }

    public function testActionParamFieldsForwardedToRoute()
    {
        $routes = Http::getRoutes();

        $route = null;
        foreach ($routes as $method => $methodRoutes) {
            foreach ($methodRoutes as $r) {
                if ($r->getPath() === '/with-params') {
                    $route = $r;
                    break 2;
                }
            }
        }

        $this->assertNotNull($route, 'Route /with-params should be registered');

        $params = $route->getParams();

        // Verify all Action::param() fields are forwarded to the Route
        $actionParamKeys = ['default', 'validator', 'description', 'optional', 'injections', 'skipValidation', 'deprecated', 'example'];

        foreach ($params as $name => $param) {
            foreach ($actionParamKeys as $key) {
                $this->assertArrayHasKey($key, $param, "Param '{$name}' is missing '{$key}' on the Route. Platform must forward all Action param fields.");
            }
        }

        $this->assertEquals('John Doe', $params['name']['example']);
        $this->assertFalse($params['name']['deprecated']);
        $this->assertEquals('true', $params['active']['example']);
        $this->assertTrue($params['active']['deprecated']);
    }
}
