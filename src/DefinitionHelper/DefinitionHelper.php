<?php
declare(strict_types = 1);

namespace PhpSymfonyConfig\DefinitionHelper;

use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
interface DefinitionHelper
{
    public function register(string $entryId, ContainerBuilder $container);
}
