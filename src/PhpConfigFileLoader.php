<?php
declare(strict_types = 1);

namespace Fluent;

use Fluent\DefinitionHelper\ParameterDefinitionHelper;
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
        // The container and loader variables are exposed to the included file below
        // This is done to support "traditional" PHP config files
        // @see \Symfony\Component\DependencyInjection\Loader\PhpFileLoader
        $container = $this->container;
        $loader = $this;

        $path = $this->locator->locate($resource);
        $this->setCurrentDir(dirname($path));
        $this->container->addResource(new FileResource($path));

        $definitions = require $path;

        if (!is_array($definitions)) {
            // Support for traditional PHP config files
            return;
        }

        // Process imports
        foreach ($definitions as $entryId => $definition) {
            if ($definition instanceof Import) {
                // Import the resource
                $this->import($definition->getResource());
                // Remove it from the array
                unset($definitions[$entryId]);
            }
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
