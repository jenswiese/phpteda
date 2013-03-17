<?php

namespace Phpteda\CLI\Command;

use Phpteda\CLI\Helper\Table;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\DialogHelper;
use Phpteda\Util\GeneratorDirectoryIterator;
use DirectoryIterator;

/**
 * ShowCommand
 *
 * @package Phpteda\CLI\Command
 */
class ShowCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('show')
            ->setDescription('Show Generators and their settings');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws \RuntimeException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln($this->getApplication()->getLongVersion());
        $output->writeln('');

        $config = $this->getApplication()->getConfig();

        if (!$config->hasGeneratorDirectory()) {
            throw new \RuntimeException("Generator directory is not set. Please run 'init' command first.");
        }

        $output->writeln('Using: ' . $config->getGeneratorDirectory());

        $iterator = new GeneratorDirectoryIterator(new DirectoryIterator($config->getGeneratorDirectory()));

        $table = Table::create($output, 132);
        foreach ($iterator as $generatorFile) {
            $table->addRow()->addColumn($generatorFile->getBasename('Generator.php'));
        }
        $table->end();
    }
}
