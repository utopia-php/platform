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
    }

    /**
     * @var Client
     */
    protected $client;

    public function testRootAction(): void
    {
        $response = $this->client->call(Client::METHOD_GET, '/');
        $this->assertIsArray($response);
        $this->assertEquals('Hello World!', $response['body']);
    }

    public function testChunkedAction(): void
    {
        $response = $this->client->call(Client::METHOD_GET, '/chunked');
        $this->assertIsArray($response);
        $this->assertEquals('Hello World!', $response['body']);
    }

    public function testRedirectAction(): void
    {
        $response = $this->client->call(Client::METHOD_GET, '/redirect');
        $this->assertIsArray($response);
        $this->assertEquals('Hello World!', $response['body']);
    }

    public function testHook(): void
    {
        $response = $this->client->call(Client::METHOD_GET, '/');
        $this->assertIsArray($response);
        $this->assertEquals('Hello World!', $response['body']);
        $this->assertIsArray($response['headers']);
        $this->assertEquals('init-called', $response['headers']['x-init']);

        $response = $this->client->call(Client::METHOD_GET, '/chunked');
        $this->assertIsArray($response);
        $this->assertEquals('Hello World!', $response['body']);
        $this->assertIsArray($response['headers']);
        $this->assertEquals('', ($response['headers']['x-init'] ?? ''));
    }
}
