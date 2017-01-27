<?php
declare(strict_types = 1);

namespace PhpSymfonyConfig\Test;

use PhpSymfonyConfig\PhpConfigFileLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class PhpConfigFileLoaderTest extends \PHPUnit_Framework_TestCase
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
     * @test
     * @expectedException \Exception
     * @expectedExceptionMessageRegExp #The configuration file .+/tests/Fixtures/empty-file.php must return an array#
     */
    public function verifies_that_config_files_return_an_array()
    {
        $container = new ContainerBuilder;
        $loader = new PhpConfigFileLoader($container, new FileLocator);

        $loader->load(__DIR__ . '/Fixtures/empty-file.php');
    }
}
