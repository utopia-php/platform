<?php

namespace Utopia\Tests;

use Utopia\Platform\Action;
use Utopia\Validator\Boolean;
use Utopia\Validator\Range;
use Utopia\Validator\Text;

class TestActionWithParams extends Action
{
    public function __construct()
    {
        $this->httpPath = '/with-params';
        $this->httpMethod = 'GET';

        $this
            ->param('name', '', new Text(128), 'User name.', false, example: 'John Doe')
            ->param('age', 0, new Range(0, 150), 'User age.', true, example: '25')
            ->param('active', false, new Boolean(true), 'Is active.', true, deprecated: true, example: 'true')
            ->param('email', '', new Text(256), 'User email.', true, aliases: ['emailAddress', 'userEmail'], example: 'user@example.com')
            ->inject('response')
            ->callback(function ($name, $age, $active, $email, $response) {
                $response->send('OK');
            });
    }
}
