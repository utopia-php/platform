<?php

namespace Utopia\Tests;

use Utopia\Response;

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
}
