<?php

namespace Fluent\Test;

use Fluent\DefinitionHelper\CreateDefinitionHelper;
use function Fluent\create;

/**
 * Tests the helper functions.
 */
class FunctionsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function including_functions_twice_should_not_error()
    {
        include __DIR__ . '/../src/functions.php';
        include __DIR__ . '/../src/functions.php';
    }

    /**
     * @test
     */
    public function create_returns_a_helper()
    {
        $helper = create();

        $this->assertTrue($helper instanceof CreateDefinitionHelper);
    }
}
