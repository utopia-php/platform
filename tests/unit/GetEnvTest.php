<?php

namespace Utopia\Unit;

use PHPUnit\Framework\TestCase;
use Utopia\Tests\TestPlatform;

class GetEnvTest extends TestCase
{
    public function testGetEnv()
    {
        $platform = new TestPlatform();
        $this->assertEquals(3, $platform->getEnv('argc'));
    }
}
