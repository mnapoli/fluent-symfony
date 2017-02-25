<?php

namespace Fluent\Test;

use function Fluent\autowire;
use function Fluent\create;
use Fluent\PhpConfigLoader;
use stdClass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Test autowire() definitions.
 */
class AutowireTest extends BaseContainerTest
{
    /** @test */
    public function service_can_be_created_as_autowired()
    {
        $autowiredClass = new class(new stdClass) {
            public function __construct(stdClass $argument)
            {
                $this->argument = $argument;
            }
        };
        $autowiredClassName = get_class($autowiredClass);

        $container = $this->createContainerWithConfig([
            stdClass::class => create(stdClass::class),
            'foo' => autowire($autowiredClassName),
        ]);

        self::assertInstanceOf(stdClass::class, $container->get('foo')->argument);
    }

    /** @test */
    public function service_can_be_created_as_autowired_without_class_name()
    {
        $autowiredClass = new class(new stdClass) {
            public function __construct(stdClass $argument = null)
            {
                $this->argument = $argument;
            }
        };
        $autowiredClassName = get_class($autowiredClass);

        $container = $this->createContainerWithConfig([
            stdClass::class => create(stdClass::class),
            $autowiredClassName => autowire(),
        ]);

        self::assertInstanceOf(stdClass::class, $container->get($autowiredClassName)->argument);
    }

    /** @test */
    public function autowired_services_can_be_tagged()
    {
        $container = new ContainerBuilder;
        (new PhpConfigLoader($container))->load([
            'bar' => autowire('stdClass')
                ->tag('foo'),
        ]);
        $container->compile();

        self::assertTrue($container->findDefinition('bar')->hasTag('foo'));
        self::assertArrayHasKey('bar', $container->findTaggedServiceIds('foo'));
    }

    /** @test */
    public function autowired_services_can_be_tagged_with_attributes()
    {
        $container = new ContainerBuilder;
        (new PhpConfigLoader($container))->load([
            'bar' => autowire('stdClass')
                ->tag('foo', ['alias' => 'baz']),
        ]);
        $container->compile();

        $tagged = $container->findTaggedServiceIds('foo');
        self::assertArrayHasKey('alias', $tagged['bar'][0]);
        self::assertEquals('baz', $tagged['bar'][0]['alias']);
    }

    /** @test */
    public function autowired_services_can_be_tagged_multiple_times()
    {
        $container = new ContainerBuilder;
        (new PhpConfigLoader($container))->load([
            'bar' => autowire('stdClass')
                ->tag('foo')
                ->tag('baz'),
        ]);
        $container->compile();

        self::assertTrue($container->findDefinition('bar')->hasTag('foo'));
        self::assertTrue($container->findDefinition('bar')->hasTag('baz'));
    }
}
