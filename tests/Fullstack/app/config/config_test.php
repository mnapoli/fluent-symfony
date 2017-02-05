<?php

use function Fluent\extension;
use function Fluent\import;

return [

    import('config_dev.php'),

    extension('framework', [
        'test' => null,
        'session' => [
            'storage_id' => 'session.storage.mock_file',
        ],
        'profiler' => [
            'collect' => false,
        ],
    ]),

];
