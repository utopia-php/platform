<?php

namespace Utopia\Tests;

use PHPUnit\Framework\TestCase;
use Utopia\CLI\Adapters\Generic;
use Utopia\CLI\CLI;
use Utopia\DI\Container;
use Utopia\Platform\Service;

class CLITest extends TestCase
{
    public function setUp(): void
    {
    }

    public function tearDown(): void
    {
    }

    public function testCLISetup()
    {
        ob_start();

        $cli = new CLI(new Generic(), ['test.php', 'build', '--email=me@example.com', '--list=item1', '--list=item2']); // Mock command request

        $platform = new TestPlatform();
        $platform->setCli($cli);
        $platform->init(Service::TYPE_TASK);

        $cli = $platform->getCli();
        $cli->run();

        $result = ob_get_clean();

        $this->assertEquals('me@example.com-item1-item2', $result);
        $this->assertCount(3, $cli->getTasks());
    }

    public function testCLISetupWithProvidedContainer()
    {
        $argv = $_SERVER['argv'] ?? [];

        try {
            $_SERVER['argv'] = ['test.php', 'inject'];
            ob_start();

            $container = new Container();
            $container->set('test', fn () => 'test-value');

            $platform = new TestPlatform();
            $platform->init(Service::TYPE_TASK, [
                'adapter' => new Generic(),
                'container' => $container,
            ]);

            $cli = $platform->getCli();
            $cli->run();

            $result = ob_get_clean();
        } finally {
            $_SERVER['argv'] = $argv;
        }

        $this->assertEquals('test-value', $result);
    }
}
