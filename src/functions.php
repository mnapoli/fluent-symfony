<?php
declare(strict_types = 1);

namespace PhpSymfonyConfig;

use PhpSymfonyConfig\DefinitionHelper\AliasDefinitionHelper;
use PhpSymfonyConfig\DefinitionHelper\CreateDefinitionHelper;
use Symfony\Component\DependencyInjection\Reference;

if (!function_exists('PhpSymfonyConfig\create')) {
    /**
     * Helper for defining an object.
     *
     * @param string|null $className Class name of the object.
     *                               If null, the name of the entry (in the container) will be used as class name.
     */
    function create(string $className = null) : CreateDefinitionHelper
    {
        return new CreateDefinitionHelper($className);
    }
}

if (!function_exists('PhpSymfonyConfig\alias')) {
    /**
     * Helper for defining an alias.
     */
    function alias(string $targetEntryId) : AliasDefinitionHelper
    {
        return new AliasDefinitionHelper($targetEntryId);
    }
}

if (!function_exists('PhpSymfonyConfig\get')) {
    /**
     * Reference to another service.
     */
    function get(string $entryId) : Reference
    {
        return new Reference($entryId);
    }
}
