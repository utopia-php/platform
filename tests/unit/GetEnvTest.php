<?php

namespace Utopia\Unit;

use PHPUnit\Framework\TestCase;

class GetEnvTest extends TestCase
{
    public function testGetEnv(): void
    {
        $platform = new Mock();

        $this->assertSame($_SERVER['argc'] ?? null, $platform->getEnv('argc'));
        $this->assertSame('fallback', $platform->getEnv('UTOPIA_PLATFORM_MISSING_ENV', 'fallback'));
        $this->assertNull($platform->getEnv('UTOPIA_PLATFORM_MISSING_ENV'));
    }
}
