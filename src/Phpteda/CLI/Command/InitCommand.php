<?php

namespace Phpteda\CLI\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\DialogHelper;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

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
            ->setDescription('Initialize project settings');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln($this->getApplication()->getLongVersion());
        $output->writeln('');

        $this->askBootstrapPathname($output);
        $this->askGeneratorDirectory($output);

        $this->getApplication()->getConfig();
        $output->writeln('<info>Configuration is written.</info>');
    }

    /**
     * @param OutputInterface $output
     */
    protected function askGeneratorDirectory(OutputInterface $output)
    {
        $dialog = new DialogHelper();
        $config = $this->getApplication()->getConfig();

        while (true) {
            $directory = $dialog->ask(
                $output,
                "<question>Provide directory for Generators:</question> [" . $config->getGeneratorDirectory() . "] ",
                $config->getGeneratorDirectory(),
                $this->getAvailableGeneratorDirectories()
            );

            if (is_dir(realpath($directory))) {
                $output->writeln("Writing '" . realpath($directory) . "' to configfile.");
                $config->setGeneratorDirectory(realpath($directory));
                break;
            }
            $output->writeln('<error>This is not a valid directory.</error>');
        }
    }

    /**
     * @param OutputInterface $output
     */
    protected function askBootstrapPathname(OutputInterface $output)
    {
        $dialog = new DialogHelper();
        $config = $this->getApplication()->getConfig();

        while (true) {
            $bootstrapPathname = $dialog->ask(
                $output,
                "<question>Provide path for bootstrap file:</question> [" . $config->getBootstrapPathname() . "] ",
                $config->getBootstrapPathname(),
                $this->getAvailableBootstrapPathnames()
            );

            if (empty($bootstrapPathname)) {
                break;
            }

            if (is_file(realpath($bootstrapPathname))) {
                $output->writeln("Writing '" . realpath($bootstrapPathname) . "' to configfile.");
                $config->setBootstrapPathname(realpath($bootstrapPathname));
                break;
            }
            $output->writeln('<error>This is not a valid file.</error>');
        }
    }

    /**
     * @return array
     */
    protected function getAvailableBootstrapPathnames()
    {
        $bootstrapPathnames = array();
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(getcwd()));

        foreach ($iterator as $entry) {
            if ('bootstrap.php' != $entry->getBasename()) {
                continue;
            }

            $bootstrapPathnames[] = $entry->getPathname();
        }

        return $bootstrapPathnames;
    }

    /**
     * Retrieves directories that are most likely made to hold generators
     *
     * @return array
     */
    protected function getAvailableGeneratorDirectories()
    {
        $directories = array();

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(
                getcwd(),
                RecursiveDirectoryIterator::SKIP_DOTS
            ),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $entry) {
            $containsGenerator = (false !== strpos(strtolower($entry->getFilename()), 'generator'));
            if (!$entry->isDir() || !$containsGenerator) {
                continue;
            }

            $directories[] = $entry->getPathname();
        }

        return $directories;
    }
}
