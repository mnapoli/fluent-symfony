<?php

namespace Fluent\Test;

use Fluent\PhpConfigLoader;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use function Fluent\alias;
use function Fluent\create;

/**
 * Test alias() definitions.
 */
class AliasTest extends TestCase
{
    public function test_alias_service()
    {
        $container = new ContainerBuilder;
        (new PhpConfigLoader($container))->load([
            'foo' => alias('bar'),
            'bar' => create('stdClass'),
        ]);
        self::assertInstanceOf('stdClass', $container->get('foo'));
    }
}
