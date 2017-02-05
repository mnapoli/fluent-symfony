<?php
declare(strict_types = 1);

namespace AppBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class TestCommand extends Command
{
    protected function configure()
    {
        $this->setName('test');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Hello');
    }
}
