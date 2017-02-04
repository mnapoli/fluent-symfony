<?php

namespace Fluent\Test;

use function Fluent\autowire;
use function Fluent\create;
use Fluent\PhpConfigLoader;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Test autowire() definitions.
 */
class AutowireTest extends TestCase
{
    /**
     * @test
     */
    public function service_can_be_created_as_autowired()
    {
        $classToBeInjected   = new stdClass;
        $autowiredClass      = new class($classToBeInjected) {
            public function __construct(stdClass $argument)
            {
                $this->argument = $argument;
            }
        };
        $autowiredClassName = get_class($autowiredClass);

        $container = new ContainerBuilder;
        (new PhpConfigLoader($container))->load([
            stdClass::class => create(stdClass::class),
            'foo'           => autowire($autowiredClassName),
        ]);
        $container->compile();

        self::assertInstanceOf(stdClass::class, $container->get('foo')->argument);
    }

    /**
     * @test
     */
    public function service_can_be_created_as_autowired_without_class_name()
    {
        $classToBeInjected   = new stdClass;
        $autowiredClass      = new class($classToBeInjected) {
            public function __construct(stdClass $argument)
            {
                $this->argument = $argument;
            }
        };
        $autowiredClassName = get_class($autowiredClass);

        $container = new ContainerBuilder;
        (new PhpConfigLoader($container))->load([
            stdClass::class     => create(stdClass::class),
            $autowiredClassName => autowire(),
        ]);
        $container->compile();

        self::assertInstanceOf(stdClass::class, $container->get($autowiredClassName)->argument);
    }

    /**
     * @test
     */
    public function autowired_services_can_be_tagged()
    {
        $container = new ContainerBuilder;
        (new PhpConfigLoader($container))->load([
            'bar' => autowire('stdClass')
                ->tag('foo'),
        ]);
        self::assertTrue($container->findDefinition('bar')->hasTag('foo'));
        self::assertArrayHasKey('bar', $container->findTaggedServiceIds('foo'));
    }

    /**
     * @test
     */
    public function autowired_services_can_be_tagged_with_attributes()
    {
        $container = new ContainerBuilder;
        (new PhpConfigLoader($container))->load([
            'bar' => autowire('stdClass')
                ->tag('foo', ['alias' => 'baz']),
        ]);
        $tagged = $container->findTaggedServiceIds('foo');
        self::assertArrayHasKey('alias', $tagged['bar'][0]);
        self::assertEquals('baz', $tagged['bar'][0]['alias']);
    }

    /**
     * @test
     */
    public function autowired_services_can_be_tagged_multiple_times()
    {
        $container = new ContainerBuilder;
        (new PhpConfigLoader($container))->load([
            'bar' => autowire('stdClass')
                ->tag('foo')
                ->tag('baz'),
        ]);
        self::assertTrue($container->findDefinition('bar')->hasTag('foo'));
        self::assertTrue($container->findDefinition('bar')->hasTag('baz'));
    }
}
