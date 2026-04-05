<?php

namespace Utopia\Tests;

use Utopia\Platform\Action;

class TestActionCLIInjection extends Action
{
    public function __construct()
    {
        $this
            ->inject('test')
            ->callback(function ($test) {
                $this->action($test);
            });
    }

    public function action($test)
    {
        echo $test;
    }
}
