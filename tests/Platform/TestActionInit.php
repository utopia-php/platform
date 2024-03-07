<?php

namespace Utopia\Tests;

use Utopia\Http\Response;
use Utopia\Platform\Action;

class TestActionInit extends Action
{
    public function __construct()
    {
        $this->type = Action::TYPE_INIT;
        $this->groups(['test']);
        $this->inject('response');
        $this->callback(function ($response) {
            $this->action($response);
        });
    }

    public function action(Response $response)
    {
        $response->addHeader('x-init', 'init-called');
    }
}
