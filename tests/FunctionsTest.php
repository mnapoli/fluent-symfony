<?php

namespace Fluent\Test;

use Fluent\DefinitionHelper\CreateDefinitionHelper;
use function Fluent\create;
use PHPUnit\Framework\TestCase;

/**
 * Tests the helper functions.
 */
class FunctionsTest extends TestCase
{
    /**
     * @test
     */
    public function including_functions_twice_should_not_error()
    {
        include __DIR__ . '/../src/functions.php';
        include __DIR__ . '/../src/functions.php';

        self::assertInstanceOf(CreateDefinitionHelper::class, create());
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
