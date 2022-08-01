<?php

namespace Utopia\Tests;

use Utopia\Platform\Action;

class TestActionRoot extends Action
{
    public function __construct()
    {
        $this->httpPath = '/';
        $this->groups(['test']);
        $this->httpMethod = 'GET';
        $this->inject('response');
        $this->callback(function ($response) {
            $this->action($response);
        });
    }

    public function action($response)
    {
        $response->send('Hello World!');
    }
}
