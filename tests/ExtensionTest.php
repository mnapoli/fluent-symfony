<?php

namespace Fluent\Test;

use function Fluent\extension;
use Fluent\PhpConfigLoader;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension as BaseExtension;

/**
 * Test extension() definitions.
 */
class ExtensionTest extends TestCase
{
    /**
     * @test
     */
    public function configures_an_extension()
    {
        // The container extension class
        $fooExtension = new class() extends BaseExtension {
            public function load(array $configs, ContainerBuilder $container)
            {
                $container->setParameter('magic_parameter', $configs[0]['bar']);
            }
            public function getAlias()
            {
                return 'foo';
            }
        };

        $container = new ContainerBuilder;
        $container->registerExtension($fooExtension);
        (new PhpConfigLoader($container))->load([
            extension('foo', [
                'bar' => 'Hello world',
            ]),
        ]);
        $container->compile(); // we must compile the container so that extensions are applied

        self::assertEquals('Hello world', $container->getParameter('magic_parameter'));
    }
}
