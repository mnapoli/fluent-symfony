<?php
declare(strict_types = 1);

namespace Fluent\DefinitionHelper;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Helps defining how to create a service using a factory.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class FactoryDefinitionHelper implements DefinitionHelper
{
    /**
     * @var Definition
     */
    private $definition;

    /**
     * @param string|array $factory A PHP function or an array containing a class/Reference and a method to call
     */
    public function __construct($factory)
    {
        $this->definition = new Definition();
        $this->definition->setFactory($factory);
    }

    public function register(string $entryId, ContainerBuilder $container)
    {
        $container->setDefinition($entryId, $this->definition);
    }

    /**
     * Defines the arguments to use when calling the factory.
     *
     * This method takes a variable number of arguments, example:
     *     ->arguments($param1, $param2, $param3)
     */
    public function arguments(...$arguments) : self
    {
        $this->definition->setArguments($arguments);

        return $this;
    }
}
