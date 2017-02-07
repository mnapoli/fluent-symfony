<?php

namespace Fluent\Test;

use function Fluent\create;
use function Fluent\get;
use Fluent\PhpConfigLoader;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Compiler\DecoratorServicePass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class DecoratorTest extends TestCase
{
    /**
     * @test
     */
    public function services_can_decorate_an_existing_service() {
        $decorated = new class() {};
        $decorating = new class(null) {
            public function __construct($argument) {
                $this->argument = $argument;
            }
        };
        $decoratedClassName = get_class($decorated);
        $decoratingClassName = get_class($decorating);

        $container = new ContainerBuilder;
        (new PhpConfigLoader($container))->load([
            'decorated' => create($decoratedClassName),
            'decorating' => create($decoratingClassName)
                ->decorate('decorated')
                ->arguments(get('decorating.inner')),
        ]);
        $this->process($container);

        self::assertInstanceOf($decoratingClassName, $container->get('decorated'));
        self::assertTrue($container->has('decorating.inner'));
    }

    /**
     * @test
     */
    public function services_can_decorate_an_existing_service_while_renaming_it() {
        $decorated = new class() {};
        $decorating = new class(null) {
            public function __construct($argument) {
                $this->argument = $argument;
            }
        };
        $decoratedClassName = get_class($decorated);
        $decoratingClassName = get_class($decorating);

        $container = new ContainerBuilder;
        (new PhpConfigLoader($container))->load([
            'decorated' => create($decoratedClassName),
            'decorating' => create($decoratingClassName)
                ->decorate('decorated', 'decorating.foo')
                ->arguments(get('decorating.foo')),
        ]);
        $this->process($container);

        self::assertInstanceOf($decoratingClassName, $container->get('decorated'));
        self::assertTrue($container->has('decorating.foo'));
    }

    /**
     * @test
     */
    public function services_can_decorate_an_existing_decorated_service() {
        $decorated = new class() {};
        $decorating = new class() {};
        $decorating2 = new class() {};

        $decoratedClassName = get_class($decorated);
        $decoratingClassName = get_class($decorating);
        $decorating2ClassName = get_class($decorating2);

        $container = new ContainerBuilder;
        (new PhpConfigLoader($container))->load([
            'decorated' => create($decoratedClassName),
            'decorating' => create($decoratingClassName)
                ->decorate('decorated')
                ->arguments(get('decorating.inner'))
            ,
            'decorating2' => create($decorating2ClassName)
                ->decorate('decorated')
                ->arguments(get('decorating2.inner'))
            ,
        ]);
        $this->process($container);

        self::assertInstanceOf($decorating2ClassName, $container->get('decorated'));
        self::assertTrue($container->has('decorating.inner'));
        self::assertTrue($container->has('decorating2.inner'));
    }

    /**
     * @test
     */
    public function services_can_decorate_an_existing_decorated_service_with_priorities() {
        $decorated = new class() {};
        $decorating = new class() {};
        $decorating2 = new class() {};

        $decoratedClassName = get_class($decorated);
        $decoratingClassName = get_class($decorating);
        $decorating2ClassName = get_class($decorating2);

        $container = new ContainerBuilder;
        (new PhpConfigLoader($container))->load([
            'decorated' => create($decoratedClassName),
            'decorating' => create($decoratingClassName)
                ->decorate('decorated', null, 1)
                ->arguments(get('decorating.inner'))
            ,
            'decorating2' => create($decorating2ClassName)
                ->decorate('decorated', null, 2)
                ->arguments(get('decorating2.inner'))
            ,
        ]);
        $this->process($container);

        self::assertInstanceOf($decoratingClassName, $container->get('decorated'));
        self::assertTrue($container->has('decorating.inner'));
        self::assertTrue($container->has('decorating2.inner'));
    }

    /**
     * The container needs to be procecessed by the DecoratorServicePass to resolve decorators service ids
     *
     * @param ContainerBuilder $container
     */
    protected function process(ContainerBuilder $container) {
        $repeatedPass = new DecoratorServicePass();
        $repeatedPass->process($container);
    }
}