# Fluent configuration for Symfony

[![Build Status](https://img.shields.io/travis/mnapoli/fluent-symfony/master.svg?style=flat-square)](https://travis-ci.org/mnapoli/fluent-symfony)

**Work in progress**

This package offers an alternative configuration syntax for Symfony's container, inspired by [PHP-DI's configuration](http://php-di.org/doc/php-definitions.html).

## Why?

TODO

The main goal is to benefit from stricter analysis from the PHP engine and IDEs. If you are interested, I've also detailed [why YAML was replaced by a similar syntax in PHP-DI 5](http://php-di.org/news/06-php-di-4-0-new-definitions.html).

- auto-completion on classes or constants:

    ![](https://i.imgur.com/t65dZ9l.png)

- auto-completion when writing configuration:

    ![](http://i.imgur.com/0w0or7S.gif)

- real time validation in IDEs:

    ![](http://i.imgur.com/28wO3Oa.png)
    
- constant support:

    ![](https://i.imgur.com/LsRXbJx.png)

- better refactoring support

## Comparison with existing formats

Currently, in Symfony, you can configure the container using:

- YAML

    ```yaml
    parameters:
        mailer.transport: sendmail
    
    services:
        mailer:
            class:     Mailer
            arguments: ['%mailer.transport%']
    ```

- XML

    ```xml
    <?xml version="1.0" encoding="UTF-8" ?>
    <container xmlns="http://symfony.com/schema/dic/services"
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:schemaLocation="http://symfony.com/schema/dic/services http://symfony.com/schema/dic/services/services-1.0.xsd">
    
        <parameters>
            <parameter key="mailer.transport">sendmail</parameter>
        </parameters>
    
        <services>
            <service id="mailer" class="Mailer">
                <argument>%mailer.transport%</argument>
            </service>
        </services>
    </container>
    ```

- PHP code

    ```php
    $container->setParameter('mailer.transport', 'sendmail');
    $container
        ->register('mailer', 'Mailer')
        ->addArgument('%mailer.transport%');
    ```

With this package, you can now use a 4th alternative:

```php
return [
    'mailer.transport' => 'sendmail',

    'mailer' => create(Mailer::class)
        ->arguments('%mailer.transport%'),
];
```

## Installation

```
composer require mnapoli/fluent-symfony
```

To enable the new format in a Symfony fullstack application, simply import the `EnableFluentConfig` trait in `app/AppKernel.php`, for example:

```php
<?php

use Fluent\EnableFluentConfig;
use Symfony\Component\HttpKernel\Kernel;
// ...

class AppKernel extends Kernel
{
    use EnableFluentConfig;

    // ...
}
```

You can now either:

- write all your config in "fluent" syntax, to do that change your `AppKernel` to load `.php` files instead of `.yml`:

    ```php
    class AppKernel extends Kernel
    {
        use EnableFluentConfig;
    
        // ...
    
        public function registerContainerConfiguration(LoaderInterface $loader)
        {
            $loader->load($this->getRootDir().'/config/config_'.$this->getEnvironment().'.php');
        }
    }
    ```

- or import PHP config files from YAML config files:

    ```yaml
    imports:
        - services.php
        
    # ...
    ```

Be advised that PHP config files in the "traditional" form ([see the documentation](http://symfony.com/doc/current/components/dependency_injection.html#setting-up-the-container-with-configuration-files)) *are still supported* and will continue to work.

## Syntax

A configuration file must `return` a PHP array. In that array, parameters, services and imports are defined altogether:

```php
<?php
# app/config/config.php

return [
    // ...
];
```

## Parameters

Parameters are declared as simple values:

```php
return [
    'foo' => 'bar',
];
```

This is the same as:

```yaml
parameters:
    foo: 'bar'
```

Parameters and services can be mixed in the same array.

## Services

Services can be declared simply using the `create()` function helper:

```php
use function Fluent\create;

return [
    'mailer' => create(Mailer::class),
];
```

When calling `$container->get('mailer')` an instance of the `Mailer` class will be created and returned.

This is the same as:

```yaml
services:
    mailer:
        class: Mailer
```

#### Using the class name as the entry ID

If the container entry ID is a class name, you can skip it when calling `create()`.

```php
return [
    Mailer::class => create(),
];
```

#### Autowiring

Services can also be [automatically wired](http://symfony.com/doc/current/components/dependency_injection/autowiring.html) using the `autowire()` function helper in place of `create()`:
 
```php
use function Fluent\autowire;
 
return [
    Mailer::class => autowire(),
];
```
 
This is the same as:

```yaml
services:
    Mailer:
        class: Mailer
        autowire: true
```

#### Constructor arguments

```php
return [
    'mailer' => create(Mailer::class)
        ->arguments('smtp.google.com', 2525),
];
```

This is the same as:

```yaml
services:
    mailer:
        class: Mailer
        arguments: ['smtp.google.com', 2525]
```

#### Dependencies

Parameters can be injected using the `'%foo%'` syntax:

```php
return [
    'mailer' => create(Mailer::class)
        ->arguments('%mailer.transport%'),
];
```

This is the same as:

```yaml
services:
    mailer:
        class:     Mailer
        arguments: ['%mailer.transport%']
```

Services can be injected using the `get()` function helper:

```php
return [
    'newsletter_manager' => create(NewsletterManager::class)
        ->arguments(get('mailer')),
];
```

This is the same as:

```yaml
services:
    newsletter_manager:
        class: NewsletterManager
        arguments: ['@mailer']
```

#### Setter injection

```php
return [
    'mailer' => create(Mailer::class)
        ->method('setHostAndPort', 'smtp.google.com', 2525),
];
```

This is the same as:

```yaml
services:
    mailer:
        class: Mailer
        calls:
            - [setHostAndPort, ['smtp.google.com', 2525]]
```

#### Property injection

```php
return [
    'mailer' => create(Mailer::class)
        ->property('host', 'smtp.google.com'),
];
```

This is the same as:

```yaml
services:
    mailer:
        class: Mailer
        properties:
            host: smtp.google.com
```

## Factories

Services can be created by [factories](https://symfony.com/doc/current/service_container/factories.html) using the `factory()` function helper:

```php
use function Fluent\factory;

return [
    'newsletter_manager' => factory([NewsletterManager::class, 'create'])
        ->arguments('foo', 'bar'),
];
```

When calling `$container->get('newsletter_manager')` the result of `NewsletterManager::create('foo', 'bar')` will be returned.

This is the same as:

```yaml
services:
    newsletter_manager:
        factory: ['AppBundle\Email\NewsletterManager', 'create']
        arguments: ['foo', 'bar']
```

## Aliases

Services can be aliased using the `alias()` function helper:

```php
use function Fluent\create;
use function Fluent\alias;

return [
    'app.phpmailer' => create(PhpMailer::class),
    'app.mailer' => alias('app.phpmailer'),
];
```

When calling `$container->get('app.mailer')` the `app.phpmailer` entry will be returned.

This is the same as:

```yaml
services:
    app.phpmailer:
        class: AppBundle\Mail\PhpMailer
    app.mailer:
        alias: app.phpmailer
```

## Tags

Services can be tagged :

```php
return [
    'mailer' => create(Mailer::class)
        ->tag('foo', ['fizz' => 'buzz', 'bar' => 'baz'])
        ->tag('bar'),
];
```

This is the same as:

```yaml
services:
    mailer:
        class: Mailer
        tags:
            - {name: foo, fizz: buzz, bar: baz}
            - {name: bar}
```

## Imports

Other configuration files can be imported using the `import()` function helper:

```php
use function Fluent\import;

return [
    import('services/mailer.php'),
];
```

You will notice that the array item is not indexed by an entry ID.

This is the same as:

```yaml
imports:
    - { resource: services/mailer.yml }
```

## Extensions

Extensions (like the framework configuration for example) can be configured using the `extension()` function helper:

```php
use function Fluent\extension;

return [
    extension('framework', [
        'http_method_override' => true,
        'trusted_proxies' => ['192.0.0.1', '10.0.0.0/8'],
    ]),
];
```

This is the same as:

```yaml
framework:
    http_method_override: true
    trusted_proxies: [192.0.0.1, 10.0.0.0/8]
```
