<?php
declare(strict_types = 1);

namespace PhpSymfonyConfig\DefinitionHelper;

use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Helps defining a service alias.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class AliasDefinitionHelper implements DefinitionHelper
{
    /**
     * @var string
     */
    private $targetEntryId;

    public function __construct(string $targetEntry)
    {
        $this->targetEntryId = $targetEntry;
    }

    public function register(string $entryId, ContainerBuilder $container)
    {
        $container->setAlias($entryId, $this->targetEntryId);
    }
}
