<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateDocsCommand extends Command
{
    protected static $defaultName = 'app:generate-docs';

    protected function configure()
    {
        $this->setName('app:generate-docs')
             ->setDescription('Generate documentation');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        exec('php generate_docs.php');
        $output->writeln('Documentation PDF générée !');

        return Command::SUCCESS;
    }
}