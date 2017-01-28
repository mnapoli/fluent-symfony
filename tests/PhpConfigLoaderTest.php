<?php
declare(strict_types = 1);

namespace PhpSymfonyConfig\Test;

use PhpSymfonyConfig\PhpConfigLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class PhpConfigLoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function supports_php_arrays()
    {
        $loader = new PhpConfigLoader(new ContainerBuilder);

        self::assertTrue($loader->supports([]));
    }

    /**
     * @test
     */
    public function does_not_support_other_types()
    {
        $loader = new PhpConfigLoader(new ContainerBuilder);

        self::assertFalse($loader->supports('foo'));
    }

    /**
     * @test
     */
    public function loads_a_php_config_into_the_container()
    {
        $container = new ContainerBuilder;

        (new PhpConfigLoader($container))->load([
            'foo' => 'bar',
        ]);

        self::assertEquals('bar', $container->getParameter('foo'));
    }

    /**
     * @test
     */
    public function casts_raw_values_into_parameters()
    {
        $container = new ContainerBuilder;

        (new PhpConfigLoader($container))->load([
            'foo' => 'bar',
        ]);

        self::assertTrue($container->hasParameter('foo'));
        self::assertEquals('bar', $container->getParameter('foo'));
    }
}
