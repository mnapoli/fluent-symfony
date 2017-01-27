<?php
declare(strict_types = 1);

namespace PhpSymfonyConfig;

use PhpSymfonyConfig\DefinitionHelper\CreateDefinitionHelper;

if (!function_exists('PhpSymfonyConfig\create')) {

    /**
     * Helper for defining an object.
     *
     * @param string|null $className Class name of the object.
     *                               If null, the name of the entry (in the container) will be used as class name.
     *
     * @return CreateDefinitionHelper
     */
    function create($className = null)
    {
        return new CreateDefinitionHelper($className);
    }

}
