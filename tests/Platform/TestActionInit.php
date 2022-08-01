<?php

namespace Utopia\Tests;

use Utopia\Platform\Action;
use Utopia\Response;

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
