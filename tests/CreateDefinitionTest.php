<?php

namespace PhpSymfonyConfig\Test;

use function PhpSymfonyConfig\create;
use PhpSymfonyConfig\PhpConfigLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Test create() definitions.
 */
class CreateDefinitionTest extends \PHPUnit_Framework_TestCase
{
    public function test_create_with_class_name_provided()
    {
        $container = new ContainerBuilder;
        $loader = new PhpConfigLoader($container);
        $loader->load([
            'foo' => create('stdClass'),
        ]);
        self::assertInstanceOf('stdClass', $container->get('foo'));
    }

    public function test_create_with_class_name_as_array_key()
    {
        $container = new ContainerBuilder;
        $loader = new PhpConfigLoader($container);
        $loader->load([
            'stdClass' => create(),
        ]);
        self::assertInstanceOf('stdClass', $container->get('stdClass'));
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
        $loader = new PhpConfigLoader($container);
        $loader->load([
            $className => create()
                ->method('increment')
                ->method('increment'),
        ]);

        $class = $container->get($className);
        $this->assertEquals(2, $class->count);
    }
}
