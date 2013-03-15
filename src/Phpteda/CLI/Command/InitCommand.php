<?php

namespace Phpteda\CLI\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\DialogHelper;

/**
 * InitCommand
 *
 * @package Phpteda\CLI\Command
 */
class InitCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('init')
            ->setDescription('Initialize project settings.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dialog = new DialogHelper();

        $output->writeln($this->getApplication()->getLongVersion());
        $output->writeln('');

        while (true) {
            $directory = $dialog->ask(
                $output,
                "<question>Provide directory for Generators:</question> ",
                $this->getApplication()->getConfig()->getGeneratorDirectory()
            );
            if (is_dir(realpath($directory))) {
                $output->writeln("Writing '" . realpath($directory) . "' to configfile.");
                $this->getApplication()
                    ->getConfig()
                    ->setGeneratorDirectory(realpath($directory))
                    ->save();
                break;
            }
            $output->writeln('<error>This is not a valid directory</error>');
        }

        $output->writeln('<info>Configuration is written.</info>');
    }
}
