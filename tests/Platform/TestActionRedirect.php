<?php

namespace Utopia\Tests;

use Utopia\Platform\Action;

class TestActionRedirect extends Action
{
    public function __construct()
    {
        $this->httpPath = '/redirect';
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
        $response->redirect('/');
    }
}
