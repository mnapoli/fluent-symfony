<?php
declare(strict_types = 1);

namespace PhpSymfonyConfig;

use PhpSymfonyConfig\DefinitionHelper\DefinitionHelper;
use PhpSymfonyConfig\DefinitionHelper\ParameterDefinitionHelper;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Loads PHP configuration in Symfony's container.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class PhpConfigLoader extends Loader
{
    /**
     * @var ContainerBuilder
     */
    private $container;

    public function __construct(ContainerBuilder $container)
    {
        $this->container = $container;
    }

    public function load($resource, $type = null)
    {
        if (!is_array($resource)) {
            throw new \Exception('Invalid resource');
        }

        $definitions = $resource;

        foreach ($definitions as $entryId => $definitionHelper) {
            // Raw values are automatically turned into parameters
            if (!$definitionHelper instanceof DefinitionHelper) {
                $definitionHelper = new ParameterDefinitionHelper($definitionHelper);
            }

            // Register the definition in the container
            $definitionHelper->register($entryId, $this->container);
        }
    }

    public function supports($resource, $type = null)
    {
        return is_array($resource);
    }
}
