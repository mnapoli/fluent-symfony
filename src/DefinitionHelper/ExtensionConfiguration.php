<?php
declare(strict_types = 1);

namespace Fluent\DefinitionHelper;

use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class ExtensionConfiguration implements DefinitionHelper
{
    /**
     * @var string
     */
    private $extensionName;

    /**
     * @var array
     */
    private $configuration;

    public function __construct(string $extensionName, array $configuration)
    {
        $this->extensionName = $extensionName;
        $this->configuration = $configuration;
    }

    public function register(string $entryId, ContainerBuilder $container)
    {
        $container->loadFromExtension($this->extensionName, $this->configuration);
    }
}
