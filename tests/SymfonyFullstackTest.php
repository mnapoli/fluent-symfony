<?php

namespace Fluent\Test;

use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Test that it all works with the Symfony fullstack.
 */
class SymfonyFullstackTest extends TestCase
{
    /**
     * @test
     * @runInSeparateProcess Because we require the AppKernel file
     */
    public function php_config_works_in_symfony_fullstack()
    {
        require __DIR__.'/Fullstack/app/AppKernel.php';
        require __DIR__ . '/Fullstack/src/AppBundle/AppBundle.php';
        require __DIR__ . '/Fullstack/src/AppBundle/Command/TestCommand.php';

        $application = new Application(new \AppKernel('dev', true));
        $output = new BufferedOutput;
        $application->run(new ArrayInput(['test']), $output);

        self::assertEquals("Hello\n", $output->fetch());
    }
}
