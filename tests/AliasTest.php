<?php

namespace PhpSymfonyConfig\Test;

use PhpSymfonyConfig\PhpConfigLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use function PhpSymfonyConfig\alias;
use function PhpSymfonyConfig\create;

/**
 * Test alias() definitions.
 */
class AliasTest extends \PHPUnit_Framework_TestCase
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
