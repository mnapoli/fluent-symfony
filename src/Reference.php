<?php
declare(strict_types = 1);

namespace Fluent;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Reference as SymfonyReference;

class Reference extends SymfonyReference
{
    /**
     * Reference to another service or null if it does not exist
     *
     * @link https://symfony.com/doc/current/service_container/optional_dependencies.html#setting-missing-dependencies-to-null
     */
    public function nullIfMissing() : self
    {
        return new static((string) $this, ContainerInterface::NULL_ON_INVALID_REFERENCE);
    }

    /**
     * Same as nullIfMissing() but the method call is removed in case of setter injection
     *
     * @link https://symfony.com/doc/current/service_container/optional_dependencies.html#ignoring-missing-dependencies
     */
    public function ignoreIfMissing() : self
    {
        return new static((string) $this, ContainerInterface::IGNORE_ON_INVALID_REFERENCE);
    }

}