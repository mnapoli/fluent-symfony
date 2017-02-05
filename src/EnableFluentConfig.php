<?php
declare(strict_types = 1);

namespace Fluent;

use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Loader\IniFileLoader;
use Symfony\Component\DependencyInjection\Loader\DirectoryLoader;
use Symfony\Component\DependencyInjection\Loader\ClosureLoader;
use Symfony\Component\HttpKernel\Config\FileLocator;

/**
 * Trait that can be used in a Symfony Kernel to enable fluent configuration.
 *
 * Example:
 *
 *     class AppKernel extends Kernel
 *     {
 *         use EnableFluentConfig;
 *
 *         // ...
 *     }
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
trait EnableFluentConfig
{
    protected function getContainerLoader(ContainerInterface $container)
    {
        $locator = new FileLocator($this);
        $resolver = new LoaderResolver(array(
            new XmlFileLoader($container, $locator),
            new YamlFileLoader($container, $locator),
            new IniFileLoader($container, $locator),
            new PhpConfigFileLoader($container, $locator),
            new DirectoryLoader($container, $locator),
            new ClosureLoader($container),
        ));

        return new DelegatingLoader($resolver);
    }
}
