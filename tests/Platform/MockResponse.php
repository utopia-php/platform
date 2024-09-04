<?php

namespace Utopia\Tests;

use Utopia\Http\Response;

class MockResponse extends Response
{
    public function end(string $content = null): void
    {
        if (! is_null($content)) {
            echo $content;
        }
    }

    public function send(string $body = ''): void
    {
        $this->sent = true;
        $this->end($body);
    }

    public function chunk(string $body = '', bool $end = false): void
    {
        if ($end) {
            $this->sent = true;
        }
        $this->write($body);
        if ($end) {
            $this->end();
        }
    }

    public function write(string $content): bool
    {
        if (! is_null($content)) {
            echo $content;
        }
        return true;
    }

    protected function sendStatus(int $statusCode, string $reason): void
    {
        // TODO: Implement sendStatus() method.
    }

    public function sendHeader(string $key, string $value): void
    {
        // TODO: Implement sendHeader() method.
    }

    protected function sendCookie(string $name, string $value, array $options): void
    {
        // TODO: Implement sendCookie() method.
    }
}
