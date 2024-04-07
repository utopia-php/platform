<?php

namespace Utopia\Tests;

use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase
{
    public function setUp(): void
    {
        $this->client = new Client();
    }

    public function tearDown(): void
    {
        $this->client = null;
    }

    /**
     * @var Client
     */
    protected $client;

    public function testRootAction()
    {
        $response = $this->client->call(Client::METHOD_GET, '/');
        $this->assertEquals('Hello World!', $response['body']);
    }

    // public function testChunkedAction()
    // {
    //     $response = $this->client->call(Client::METHOD_GET, '/chunked');
    //     $this->assertEquals('Hello World!', $response['body']);
    // }

    public function testRedirectAction()
    {
        $response = $this->client->call(Client::METHOD_GET, '/redirect');
        $this->assertEquals('Hello World!', $response['body']);
    }

    public function testHook()
    {
        $response = $this->client->call(Client::METHOD_GET, '/');
        $this->assertEquals('Hello World!', $response['body']);
        $this->assertEquals('init-called', $response['headers']['x-init']);

        // $response = $this->client->call(Client::METHOD_GET, '/chunked');
        // $this->assertEquals('Hello World!', $response['body']);
        // $this->assertEquals('', ($response['headers']['x-init'] ?? ''));
    }
}
