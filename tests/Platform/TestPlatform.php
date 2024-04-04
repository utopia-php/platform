<?php

namespace Utopia\Tests;

use Utopia\Platform\Module;
use Utopia\Platform\Platform;

class TestPlatform extends Platform
{
    public function __construct()
    {
        $module = new Module();
        parent::__construct($module);

        $this->addService('testService', new TestService());
        $this->addService('testCli', new TestServiceCLI());
    }
}
