<?php

namespace Utopia\Tests;

use Utopia\Platform\Action;

class TestActionChunked extends Action
{
    public function __construct()
    {
        $this->httpPath = '/chunked';
        $this->httpMethod = 'GET';
        $this->inject('response');
        $this->callback(function ($response) {
            $this->action($response);
        });
    }

    /**
     * @param mixed $response
     */
    public function action($response): void
    {
        foreach (['Hello ', 'World!'] as $key => $word) {
            $response->chunk($word, $key == 1);
        }
    }
}
