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
        $this->askBootstrapPathname();
        $this->askGeneratorDirectory();

        $this->getApplication()->getConfig();
        $this->getIO()->writeInfo('Configuration is written.');
    }

    protected function askGeneratorDirectory()
    {
        while (true) {
            $directory = $this->getIO()->ask(
                'Provide directory for Generators',
                $this->getConfig()->getGeneratorDirectory()
            );

            if (is_dir(realpath($directory))) {
                $this->getIO()->writeInfo("Writing '" . realpath($directory) . "' to configfile.");
                $this->getConfig()->setGeneratorDirectory(realpath($directory));
                break;
            }
            $this->getIO()->writeError('This is not a valid directory.');
        }
    }

    protected function askBootstrapPathname()
    {
        while (true) {
            $bootstrapPathname = $this->getIO()->ask(
                'Provide path for bootstrap file',
                $this->getConfig()->getBootstrapPathname()
            );

            if (empty($bootstrapPathname)) {
                break;
            }

            if (is_file(realpath($bootstrapPathname))) {
                $this->getIO()->writeInfo("Writing '" . realpath($bootstrapPathname) . "' to configfile.");
                $this->getConfig()->setBootstrapPathname(realpath($bootstrapPathname));
                break;
            }
            $this->getIO()->writeError('This is not a valid file.');
        }
    }
}
