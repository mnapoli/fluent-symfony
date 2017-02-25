<?php

namespace Fluent\Test;

use function Fluent\create;
use function Fluent\get;

class DecoratorTest extends BaseContainerTest
{
    /** @test */
    public function services_can_decorate_an_existing_service()
    {
        $decorated = new class() {};
        $decorating = new class(null) {
            public function __construct($argument) {
                $this->argument = $argument;
            }
        };
        $decoratedClassName = get_class($decorated);
        $decoratingClassName = get_class($decorating);

        $container = $this->createContainerWithConfig([
            'decorated' => create($decoratedClassName),
            'decorating' => create($decoratingClassName)
                ->decorate('decorated')
                ->arguments(get('decorating.inner')),
        ]);

        self::assertInstanceOf($decoratingClassName, $container->get('decorated'));
        self::assertInstanceOf($decoratedClassName, $container->get('decorated')->argument);
        self::assertSame($container->get('decorated'), $container->get('decorating'));
    }

    /** @test */
    public function services_can_decorate_an_existing_service_while_renaming_it()
    {
        $decorated = new class() {};
        $decorating = new class(null) {
            public function __construct($argument) {
                $this->argument = $argument;
            }
        };
        $decoratedClassName = get_class($decorated);
        $decoratingClassName = get_class($decorating);

        $container = $this->createContainerWithConfig([
            'decorated' => create($decoratedClassName),
            'decorating' => create($decoratingClassName)
                ->decorate('decorated', 'decorated.foo')
                ->arguments(get('decorated.foo')),
        ]);

        self::assertInstanceOf($decoratingClassName, $container->get('decorated'));
        self::assertInstanceOf($decoratedClassName, $container->get('decorated')->argument);
    }

    /** @test */
    public function services_can_decorate_an_already_decorated_service()
    {
        $decorated = new class() {};
        $decorating = new class(null) {
            public function __construct($argument) {
                $this->argument = $argument;
            }
        };
        $decorating2 = new class(null) {
            public function __construct($argument) {
                $this->argument = $argument;
            }
        };

        $decoratedClassName = get_class($decorated);
        $decoratingClassName = get_class($decorating);
        $decorating2ClassName = get_class($decorating2);

        $container = $this->createContainerWithConfig([
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

        self::assertInstanceOf($decorating2ClassName, $container->get('decorated'));
        self::assertInstanceOf($decoratingClassName, $container->get('decorated')->argument);
        self::assertInstanceOf($decoratedClassName, $container->get('decorated')->argument->argument);
    }

    /** @test */
    public function services_can_decorate_an_existing_decorated_service_with_priorities()
    {
        $decorated = new class() {};
        $decorating = new class(null) {
            public function __construct($argument) {
                $this->argument = $argument;
            }
        };
        $decorating2 = new class(null) {
            public function __construct($argument) {
                $this->argument = $argument;
            }
        };

        $decoratedClassName = get_class($decorated);
        $decoratingClassName = get_class($decorating);
        $decorating2ClassName = get_class($decorating2);

        $container = $this->createContainerWithConfig([
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

        self::assertInstanceOf($decoratingClassName, $container->get('decorated'));
        self::assertInstanceOf($decorating2ClassName, $container->get('decorated')->argument);
        self::assertInstanceOf($decoratedClassName, $container->get('decorated')->argument->argument);
    }
}
