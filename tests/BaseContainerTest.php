<?php

namespace Fluent\Test;

use Fluent\PhpConfigLoader;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

abstract class BaseContainerTest extends TestCase
{
    public function createContainerWithConfig(array $config) : ContainerBuilder
    {
        $container = new ContainerBuilder;
        (new PhpConfigLoader($container))->load($config);

        // The container must always be compiled to ensure we test in realistic conditions
        // If we don't do that several edge cases are not covered
        $container->compile();

        return $container;
    }
}
