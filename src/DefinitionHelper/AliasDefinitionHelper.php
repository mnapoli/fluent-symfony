<?php
declare(strict_types = 1);

namespace Fluent\DefinitionHelper;

use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Helps defining a service alias.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class AliasDefinitionHelper implements DefinitionHelper
{
    /**
     * @var Alias
     */
    private $alias;

    public function __construct(string $targetEntry)
    {
        $this->alias = new Alias($targetEntry);
    }

    /**
     * Marks the alias as private
     */
    public function private() : self
    {
        $this->alias->setPublic(false);

        return $this;
    }

    public function register(string $entryId, ContainerBuilder $container)
    {
        $container->setAlias($entryId, $this->alias);
    }
}
