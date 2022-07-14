<?php

namespace Utopia\Tests;

use Utopia\Platform\Action;
use Utopia\Validator\Text;
use Utopia\Validator\ArrayList;

class TestActionCLI extends Action
{
    public function __construct()
    {
        $this
            ->param('email', null, new Text(0), '')
            ->param('list', null, new ArrayList(new Text(256)), 'List of strings')
            ->callback(function ($email, $list) {
                $this->action($email, $list);
            });
    }

    public function action($email, $list)
    {
        echo $email . '-' . implode('-', $list);
    }
}
