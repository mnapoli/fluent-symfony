<?php

namespace Fluent\Test;

use function Fluent\create;
use function Fluent\get;

/**
 * Test create() definitions.
 */
class CreateTest extends BaseContainerTest
{
    public function test_create_with_class_name_provided()
    {
        $container = $this->createContainerWithConfig([
            'foo' => create('stdClass'),
        ]);
        self::assertInstanceOf('stdClass', $container->get('foo'));
    }

    public function test_create_with_class_name_as_array_key()
    {
        $container = $this->createContainerWithConfig([
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

        $container = $this->createContainerWithConfig([
            'foo' => create($className)
                ->arguments('abc', 'def'),
        ]);
        self::assertEquals(['abc', 'def'], $container->get('foo')->arguments);
    }

    public function test_inject_in_public_property()
    {
        $fixture = new class() {
            public $foo;
        };
        $className = get_class($fixture);

        $container = $this->createContainerWithConfig([
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

        $container = $this->createContainerWithConfig([
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

        $container = $this->createContainerWithConfig([
            $className => create()
                ->method('increment')
                ->method('increment'),
        ]);

        $class = $container->get($className);
        self::assertEquals(2, $class->count);
    }

    /** @test */
    public function services_can_be_injected()
    {
        $fixture = new class(null) {
            public function __construct($argument)
            {
                $this->argument = $argument;
            }
        };
        $className = get_class($fixture);

        $container = $this->createContainerWithConfig([
            'foo' => create($className)
                ->arguments(get('stdClass')),
            'stdClass' => create(),
        ]);
        self::assertInstanceOf('stdClass', $container->get('foo')->argument);
    }

    /** @test */
    public function missing_services_can_be_injected_with_null_value()
    {
        $fixture = new class(null) {
            public function __construct($argument)
            {
                $this->argument = $argument;
            }
        };
        $className = get_class($fixture);

        $container = $this->createContainerWithConfig([
            'foo' => create($className)
                ->arguments(get('Bar')->nullIfMissing())
        ]);
        self::assertNull($container->get('foo')->argument);
    }

    /** @test */
    public function missing_services_can_be_injected_in_method_with_no_method_call()
    {
        $fixture = new class() {
            public $argument = 3;
            public function setArgument($argument)
            {
                $this->argument = $argument;
            }
        };
        $className = get_class($fixture);

        $container = $this->createContainerWithConfig([
            'foo' => create($className)
                ->method('setArgument', get('Bar')->ignoreIfMissing())
        ]);
        self::assertEquals(3, $container->get('foo')->argument);
    }

    /** @test */
    public function parameters_can_be_injected()
    {
        $fixture = new class(null) {
            public function __construct($argument)
            {
                $this->argument = $argument;
            }
        };
        $className = get_class($fixture);

        $container = $this->createContainerWithConfig([
            'foo' => create($className)
                ->arguments('%abc%'),
            'abc' => 'def',
        ]);
        self::assertEquals('def', $container->get('foo')->argument);
    }

    /** @test */
    public function services_can_be_tagged()
    {
        $container = $this->createContainerWithConfig([
            'bar' => create('stdClass')
                ->tag('foo'),
        ]);
        self::assertTrue($container->findDefinition('bar')->hasTag('foo'));
        self::assertArrayHasKey('bar', $container->findTaggedServiceIds('foo'));
    }

    /** @test */
    public function services_can_be_tagged_with_attributes()
    {
        $container = $this->createContainerWithConfig([
            'bar' => create('stdClass')
                ->tag('foo', ['alias' => 'baz']),
        ]);
        $tagged = $container->findTaggedServiceIds('foo');
        self::assertArrayHasKey('alias', $tagged['bar'][0]);
        self::assertEquals('baz', $tagged['bar'][0]['alias']);
    }

    /** @test */
    public function services_can_be_tagged_multiple_times()
    {
        $container = $this->createContainerWithConfig([
            'bar' => create('stdClass')
                ->tag('foo')
                ->tag('baz'),
        ]);
        self::assertTrue($container->findDefinition('bar')->hasTag('foo'));
        self::assertTrue($container->findDefinition('bar')->hasTag('baz'));
    }

    /** @test */
    public function services_can_be_marked_as_private()
    {
        $container = $this->createContainerWithConfig([
            'bar' => create('stdClass')
                ->private()
        ]);
        self::assertFalse($container->has('bar'));
    }

    /** @test */
    public function services_can_be_marked_as_unshared()
    {
        $container = $this->createContainerWithConfig([
            'bar' => create('stdClass')
                ->unshared()
        ]);
        self::assertFalse($container->get('bar') === $container->get('bar'));
    }

    /** @test */
    public function services_can_be_marked_as_synthetic()
    {
        $container = $this->createContainerWithConfig([
            'bar' => create()
                ->synthetic()
        ]);

        $foo = new \stdClass;
        $container->set('bar', $foo);
        self::assertInstanceOf('stdClass', $container->get('bar' ));
    }

    /** @test */
    public function services_can_be_deprecated()
    {
        $container = $this->createContainerWithConfig([
            'bar' => create('stdClass')
                ->deprecate()
        ]);
        self::assertTrue($container->findDefinition('bar')->isDeprecated());
    }

    /** @test */
    public function services_can_be_deprecated_providing_a_template_message()
    {
        $container = $this->createContainerWithConfig([
            'bar' => create('stdClass')
                ->deprecate('The "%service_id%" service is deprecated.')
        ]);
        self::assertTrue($container->findDefinition('bar')->isDeprecated());
        self::assertEquals('The "bar" service is deprecated.', $container->findDefinition('bar')->getDeprecationMessage('bar'));
    }
}
