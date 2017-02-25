<?php

namespace Fluent\Test;

use function Fluent\alias;
use function Fluent\create;

/**
 * Test alias() definitions.
 */
class AliasTest extends BaseContainerTest
{
    /** @test */
    public function services_can_be_aliased()
    {
        $container = $this->createContainerWithConfig([
            'foo' => alias('bar'),
            'bar' => create('stdClass'),
        ]);
        self::assertInstanceOf('stdClass', $container->get('foo'));
    }

    /** @test */
    public function aliases_can_be_marked_as_private()
    {
        $container = $this->createContainerWithConfig([
            'foo' => alias('bar'),
            'bar' => create('stdClass')
                ->private(),
        ]);
        self::assertInstanceOf('stdClass', $container->get('foo'));
        self::assertFalse($container->has('bar'));
    }
}
