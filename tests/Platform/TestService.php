<?php

namespace Utopia\Tests;

use Utopia\Platform\Service;

class TestService extends Service
{
    public function __construct()
    {
        $this->type = Service::TYPE_HTTP;
        $this->addAction('root', new TestActionRoot());
        $this->addAction('chunked', new TestActionChunked());
        $this->addAction('redirect', new TestActionRedirect());
        $this->addAction('initHook', new TestActionInit());
    }
}
