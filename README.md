# Fluent configuration for Symfony

This package offers a better configuration syntax for Symfony's container, inspired by [PHP-DI's configuration](http://php-di.org/doc/php-definitions.html).

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
