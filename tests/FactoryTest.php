<?php

namespace PhpSymfonyConfig\Test;

use function PhpSymfonyConfig\create;
use function PhpSymfonyConfig\factory;
use function PhpSymfonyConfig\get;
use PhpSymfonyConfig\PhpConfigLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Test factory() definitions.
 */
class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function create_a_service_using_a_factory()
    {
        $container = new ContainerBuilder;
        (new PhpConfigLoader($container))->load([
            'foo' => factory([self::class, 'foo']),
        ]);
        self::assertInstanceOf('stdClass', $container->get('foo'));
    }

    /**
     * @test
     */
    public function arguments_can_be_passed_to_the_factory()
    {
        $container = new ContainerBuilder;
        (new PhpConfigLoader($container))->load([
            'foo' => factory([self::class, 'bar'])
                ->arguments('abc', 'def'),
        ]);
        self::assertEquals('abc', $container->get('foo')->arg1);
        self::assertEquals('def', $container->get('foo')->arg2);
    }

    /**
     * @test
     */
    public function services_can_be_injected_in_arguments()
    {
        $container = new ContainerBuilder;
        (new PhpConfigLoader($container))->load([
            'foo' => factory([self::class, 'bar'])
                ->arguments(get('abc'), ''),
            'abc' => create('stdClass'),
        ]);
        self::assertInstanceOf('stdClass', $container->get('foo')->arg1);
    }

    public static function foo()
    {
        return new \stdClass();
    }

    public static function bar($arg1, $arg2)
    {
        $obj = new \stdClass();
        $obj->arg1 = $arg1;
        $obj->arg2 = $arg2;
        return $obj;
    }
}
