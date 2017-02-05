<?php

use function Fluent\extension;
use function Fluent\import;

return [

    import('config.php'),

    'locale' => 'en',

    extension('framework', [
        'router' => [
            'resource' => '%kernel.root_dir%/config/routing_dev.yml',
            'strict_requirements' => true,
        ],
    ]),

];
