<?php
declare(strict_types = 1);

namespace Fluent\Test;

use Fluent\PhpConfigFileLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ImportTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function import_another_configuration_file()
    {
        $container = new ContainerBuilder;
        $loader = new PhpConfigFileLoader($container, new FileLocator);

        $loader->load(__DIR__ . '/Fixtures/import.php');

        self::assertEquals('bar', $container->getParameter('foo'));
    }

    /**
     * @test
     */
    public function entries_defined_after_the_import_override_those_imported()
    {
        $container = new ContainerBuilder;
        $loader = new PhpConfigFileLoader($container, new FileLocator);

        $loader->load(__DIR__ . '/Fixtures/import-with-override-1.php');

        self::assertEquals('baz', $container->getParameter('foo'));
    }

    /**
     * @test
     */
    public function entries_defined_before_the_import_override_those_imported()
    {
        $container = new ContainerBuilder;
        $loader = new PhpConfigFileLoader($container, new FileLocator);

        $loader->load(__DIR__ . '/Fixtures/import-with-override-2.php');

        self::assertEquals('baz', $container->getParameter('foo'));
    }
}
