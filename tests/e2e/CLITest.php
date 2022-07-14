<?php

/**
 * Utopia PHP Framework
 *
 * @package CLI
 * @subpackage Tests
 *
 * @link https://github.com/utopia-php/framework
 * @author Eldad Fux <eldad@appwrite.io>
 * @version 1.0 RC4
 * @license The MIT License (MIT) <http://www.opensource.org/licenses/mit-license.php>
 */

namespace Utopia\Tests;

use Utopia\CLI\CLI;
use PHPUnit\Framework\TestCase;
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

        $cli = new CLI(['test.php', 'build', '--email=me@example.com', '--list=item1', '--list=item2']); // Mock command request

        $platform = new TestPlatform();
        $platform->setCli($cli);
        $platform->init(Service::TYPE_CLI);

        $cli = $platform->getCli();
        $cli->run();

        $result = ob_get_clean();

        $this->assertEquals('me@example.com-item1-item2', $result);
        $this->assertCount(2, $cli->getTasks());
    }
}
