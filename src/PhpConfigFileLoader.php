<?php
declare(strict_types = 1);

namespace PhpSymfonyConfig;

use PhpSymfonyConfig\DefinitionHelper\ParameterDefinitionHelper;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\FileLoader;

/**
 * Loads PHP configuration files in Symfony's container.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class PhpConfigFileLoader extends FileLoader
{
    /**
     * @var PhpConfigLoader
     */
    private $loader;

    public function __construct(ContainerBuilder $container, FileLocatorInterface $locator)
    {
        parent::__construct($container, $locator);

        $this->loader = new PhpConfigLoader($container);
    }

    public function load($resource, $type = null)
    {
        $path = $this->locator->locate($resource);
        $this->setCurrentDir(dirname($path));
        $this->container->addResource(new FileResource($path));

        $definitions = require $path;

        if (!is_array($definitions)) {
            throw new \Exception(sprintf('The configuration file %s must return an array', $path));
        }

        $this->loader->load($definitions);
    }

    public function supports($resource, $type = null)
    {
        if (!is_string($resource)) {
            return false;
        }

        if (null === $type && 'php' === pathinfo($resource, PATHINFO_EXTENSION)) {
            return true;
        }

        return 'php' === $type;
    }
}
