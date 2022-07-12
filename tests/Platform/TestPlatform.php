<?php

namespace Utopia\Tests;

use Utopia\Platform\Platform;

class TestPlatform extends Platform
{
    public function __construct()
    {
        $this->addService('testService', new TestService());
    }
}
