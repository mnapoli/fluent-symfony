<?php

namespace Fluent\Test;

use function Fluent\create;
use function Fluent\factory;
use function Fluent\get;
use Fluent\PhpConfigLoader;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Test that it all works with the Symfony fullstack.
 */
class SymfonyFullstackTest extends TestCase
{
    /**
     * @test
     */
    public function php_config_works_in_symfony_fullstack()
    {
        $output = shell_exec(__DIR__ . '/Fullstack/bin/console test 2>&1');

        self::assertEquals("Hello\n", $output);
    }
}
