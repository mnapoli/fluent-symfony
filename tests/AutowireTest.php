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
class AutowireTest extends \PHPUnit_Framework_TestCase
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
}