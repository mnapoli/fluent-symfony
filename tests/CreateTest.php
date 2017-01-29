<?php

namespace PhpSymfonyConfig\Test;

use function PhpSymfonyConfig\create;
use function PhpSymfonyConfig\get;
use PhpSymfonyConfig\PhpConfigLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Test create() definitions.
 */
class CreateTest extends \PHPUnit_Framework_TestCase
{
    public function test_create_with_class_name_provided()
    {
        $container = new ContainerBuilder;
        (new PhpConfigLoader($container))->load([
            'foo' => create('stdClass'),
        ]);
        self::assertInstanceOf('stdClass', $container->get('foo'));
    }

    public function test_create_with_class_name_as_array_key()
    {
        $container = new ContainerBuilder;
        (new PhpConfigLoader($container))->load([
            'stdClass' => create(),
        ]);
        self::assertInstanceOf('stdClass', $container->get('stdClass'));
    }

    public function test_inject_constructor_arguments()
    {
        $fixture = new class() {
            public function __construct()
            {
                $this->arguments = func_get_args();
            }
        };
        $className = get_class($fixture);

        $container = new ContainerBuilder;
        (new PhpConfigLoader($container))->load([
            'foo' => create($className)
                ->constructor('abc', 'def'),
        ]);
        self::assertEquals(['abc', 'def'], $container->get('foo')->arguments);
    }

    public function test_inject_in_public_property()
    {
        $fixture = new class() {
            public $foo;
        };
        $className = get_class($fixture);

        $container = new ContainerBuilder;
        (new PhpConfigLoader($container))->load([
            'foo' => create($className)
                ->property('foo', 'bar'),
        ]);
        self::assertEquals('bar', $container->get('foo')->foo);
    }

    public function test_inject_in_method()
    {
        $fixture = new class() {
            public function setSomething()
            {
                $this->arguments = func_get_args();
            }
        };
        $className = get_class($fixture);

        $container = new ContainerBuilder;
        (new PhpConfigLoader($container))->load([
            'foo' => create($className)
                ->method('setSomething', 'abc', 'def'),
        ]);
        self::assertEquals(['abc', 'def'], $container->get('foo')->arguments);
    }

    public function test_same_method_can_be_called_multiple_times()
    {
        $fixture = new class() {
            public $count = 0;
            public function increment()
            {
                $this->count++;
            }
        };
        $className = get_class($fixture);

        $container = new ContainerBuilder;
        (new PhpConfigLoader($container))->load([
            $className => create()
                ->method('increment')
                ->method('increment'),
        ]);

        $class = $container->get($className);
        $this->assertEquals(2, $class->count);
    }

    public function test_inject_service()
    {
        $fixture = new class(null) {
            public function __construct($argument)
            {
                $this->argument = $argument;
            }
        };
        $className = get_class($fixture);

        $container = new ContainerBuilder;
        (new PhpConfigLoader($container))->load([
            'foo' => create($className)
                ->constructor(get('stdClass')),
            'stdClass' => create(),
        ]);
        self::assertInstanceOf('stdClass', $container->get('foo')->argument);
    }
}
