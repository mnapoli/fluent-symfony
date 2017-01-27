<?php
declare(strict_types = 1);

namespace PhpSymfonyConfig\DefinitionHelper;

use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Helps define a parameter.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class ParameterDefinitionHelper implements DefinitionHelper
{
    /**
     * Parameter value.
     *
     * @var mixed
     */
    private $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function register(string $entryId, ContainerBuilder $container)
    {
        $container->setParameter($entryId, $this->value);
    }
}
