<?php

namespace Phpteda\CLI\Command;

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
        $this->getIO()->write($this->getApplication()->getLongVersion());
        $this->getIO()->write('');

        $this->askBootstrapPathname();
        $this->askGeneratorDirectory();

        $this->getApplication()->getConfig();
        $this->getIO()->write('<info>Configuration is written.</info>');
    }

    protected function askGeneratorDirectory()
    {
        while (true) {
            $directory = $this->getIO()->askWithSuggestions(
                "<question>Provide directory for Generators:</question> [" . $this->getConfig()->getGeneratorDirectory() . "] ",
                $this->getAvailableGeneratorDirectories(),
                $this->getConfig()->getGeneratorDirectory()
            );

            if (is_dir(realpath($directory))) {
                $this->getIO()->write("Writing '" . realpath($directory) . "' to configfile.");
                $this->getConfig()->setGeneratorDirectory(realpath($directory));
                break;
            }
            $this->getIO()->write('<error>This is not a valid directory.</error>');
        }
    }

    protected function askBootstrapPathname()
    {
        while (true) {
            $bootstrapPathname = $this->getIO()->askWithSuggestions(
                "<question>Provide path for bootstrap file:</question> [" . $this->getConfig()->getBootstrapPathname() . "] ",
                $this->getAvailableBootstrapPathnames(),
                $this->getConfig()->getBootstrapPathname()
            );

            if (empty($bootstrapPathname)) {
                break;
            }

            if (is_file(realpath($bootstrapPathname))) {
                $this->getIO()->write("Writing '" . realpath($bootstrapPathname) . "' to configfile.");
                $this->getConfig()->setBootstrapPathname(realpath($bootstrapPathname));
                break;
            }
            $this->getIO()->write('<error>This is not a valid file.</error>');
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
            new RecursiveDirectoryIterator(getcwd(), RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $entry) {
            $isGeneratorFile = (strpos($entry->getFilename(), 'Generator.php') !== false);
            $alreadyRetrieved = in_array($entry->getPath(), $directories);

            if ($entry->isDir() || !$isGeneratorFile || $alreadyRetrieved) {
                continue;
            }

            $directories[] = $entry->getPath();
        }

        return $directories;
    }
}
