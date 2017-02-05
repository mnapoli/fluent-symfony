<?php

use Composer\Autoload\ClassLoader;

/** @var ClassLoader $loader */
$loader = require __DIR__ . '/../../../vendor/autoload.php';

require __DIR__ . '/AppKernel.php';
require __DIR__ . '/../src/AppBundle/AppBundle.php';
require __DIR__ . '/../src/AppBundle/Command/TestCommand.php';

return $loader;
