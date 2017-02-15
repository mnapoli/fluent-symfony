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

    /**
     * @test
     */
    public function alias_can_be_marked_as_private()
    {
        $container = new ContainerBuilder;
        (new PhpConfigLoader($container))->load([
            'foo' => alias('bar')
                ->private(),
            'bar' => create('stdClass'),
        ]);

        self::assertFalse($container->getAlias('foo')->isPublic());
        self::assertInstanceOf('stdClass', $container->get('foo'));
    }
}
