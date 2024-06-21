<?php

namespace Utopia\Unit;

use PHPUnit\Framework\TestCase;

class GetEnvTest extends TestCase
{
    public function testGetEnv()
    {
        $platform = new Mock();
        $this->assertEquals(3, $platform->getEnv('argc'));
    }
}
