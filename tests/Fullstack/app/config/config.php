<?php

use function Fluent\extension;
use function Fluent\import;

return [

    import('parameters.yml'),

    'locale' => 'en',

    extension('framework', [
        'secret' => '%secret%',
        'router' => [
            'resource' => '%kernel.root_dir%/config/routing.yml',
            'strict_requirements' => null,
        ],
        'form' => null,
        'csrf_protection' => null,
        'validation' => [
            'enable_annotations' => true,
        ],
        'default_locale' => '%locale%',
        'trusted_hosts' => null,
        'trusted_proxies' => null,
        'session' => [
            'handler_id' => 'session.handler.native_file',
            'save_path' => '%kernel.root_dir%/../var/sessions/%kernel.environment%',
        ],
        'fragments' => null,
        'http_method_override' => true,
        'assets' => null,
        'php_errors' => [
            'log' => true,
        ],
    ]),

];
