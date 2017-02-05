<?php
declare(strict_types = 1);

namespace Fluent\Test;

use Fluent\PhpConfigFileLoader;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class PhpConfigFileLoaderTest extends TestCase
{
    /**
     * @test
     */
    public function supports_php_files()
    {
        $loader = new PhpConfigFileLoader(new ContainerBuilder, new FileLocator);

        self::assertTrue($loader->supports('foo.php'));
    }

    /**
     * @test
     */
    public function does_not_support_files_that_are_not_php_files()
    {
        $loader = new PhpConfigFileLoader(new ContainerBuilder, new FileLocator);

        self::assertFalse($loader->supports('foo.foo'));
    }

    /**
     * @test
     */
    public function loads_a_php_config_file_into_the_container()
    {
        $container = new ContainerBuilder;
        $loader = new PhpConfigFileLoader($container, new FileLocator);

        $loader->load(__DIR__ . '/Fixtures/simple.php');

        self::assertEquals('bar', $container->getParameter('foo'));
    }

    /**
     * Tests that "traditional" PHP config files for Symfony are supported.
     *
     * Since the loader loads all `.php` files, it will replace and override completely
     * the Symfony\Component\DependencyInjection\Loader\PhpFileLoader loader.
     *
     * As such, it must support files loaded by Symfony\Component\DependencyInjection\Loader\PhpFileLoader.
     *
     * @see \Symfony\Component\DependencyInjection\Loader\PhpFileLoader
     *
     * @test
     */
    public function supports_traditional_php_config_files()
    {
        $container = new ContainerBuilder;
        $loader = new PhpConfigFileLoader($container, new FileLocator);

        $loader->load(__DIR__ . '/Fixtures/traditional-php-config.php');

        self::assertEquals('bar', $container->getParameter('foo'));
    }
}
