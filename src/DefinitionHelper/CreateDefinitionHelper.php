<?php
declare(strict_types = 1);

namespace Fluent\DefinitionHelper;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

/**
 * Helps defining how to create an instance of a class.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class CreateDefinitionHelper implements DefinitionHelper
{
    /**
     * @var Definition
     */
    private $definition;

    /**
     * Helper for defining an object.
     *
     * @param string|null $className Class name of the object.
     *                               If null, the name of the entry (in the container) will be used as class name.
     * @param bool        $autowire  Whether the class should be autowired.
     */
    public function __construct(string $className = null, $autowire = false)
    {
        $this->definition = new Definition($className);
        if ($autowire) {
            $this->definition->setAutowired(true);
        }
    }

    public function register(string $entryId, ContainerBuilder $container)
    {
        if ($this->definition->getClass() === null) {
            $this->definition->setClass($entryId);
        }

        $container->setDefinition($entryId, $this->definition);
    }

    /**
     * Define the entry as lazy.
     *
     * A lazy entry is created only when it is used, a proxy is injected instead.
     */
    public function lazy() : self
    {
        $this->definition->setLazy(true);

        return $this;
    }

    /**
     * Defines the arguments to use to call the constructor.
     *
     * This method takes a variable number of arguments, example:
     *     ->arguments($param1, $param2, $param3)
     *
     * @param mixed ... Arguments to use for calling the constructor of the class.
     */
    public function arguments(...$arguments) : self
    {
        $this->definition->setArguments($arguments);

        return $this;
    }

    /**
     * Defines a value to inject in a property of the object.
     *
     * @param string $property Entry in which to inject the value.
     * @param mixed  $value    Value to inject in the property.
     */
    public function property(string $property, $value) : self
    {
        $this->definition->setProperty($property, $value);

        return $this;
    }

    /**
     * Defines a method to call and the arguments to use.
     *
     * This method takes a variable number of arguments after the method name, example:
     *
     *     ->method('myMethod', $param1, $param2)
     *
     * Can be used multiple times to declare multiple calls.
     *
     * @param string $method Name of the method to call.
     * @param mixed  ...     Arguments to use for calling the method.
     */
    public function method(string $method, ...$arguments) : self
    {
        $this->definition->addMethodCall($method, $arguments);

        return $this;
    }

    /**
     * Adds a tag to the current definition
     *
     * Can be used multiple times to declare multiple calls.
     *
     * @param string $name       The tag name
     * @param array  $attributes An array of attributes
     */
    public function tag(string $name, array $attributes = []) : self
    {
        $this->definition->addTag($name, $attributes);

        return $this;
    }

    /**
     * Marks the definition as private
     */
    public function private() : self
    {
        $this->definition->setPublic(false);

        return $this;
    }

    /**

     * Mark the service as deprecated
     *
     * @param  string|null  $template Template message to use if the definition is deprecated
     *
     * @throws InvalidArgumentException
     */
    public function deprecate(string $template = null) : self
    {
        $this->definition->setDeprecated(true, $template);

        return $this;
    }
}
