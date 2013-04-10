<?php

namespace Phpteda\CLI;

use Phpteda\CLI\IO\ConsoleIO;
use Symfony\Component\Console\Application as SymfonyApplication;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Phpteda\CLI\Config;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class represents console application
 *
 * @author: Jens Wiese <jens@howtrueisfalse.de>
 * @since: 2013-03-15
 */
class Application extends SymfonyApplication
{
    /** @var Config */
    protected $config;

    /** @var ConsoleIO */
    protected $io;

    /**
     * Constructor of the class
     */
    public function __construct(Config $config)
    {
        $name = "phpteda";
        $version = "1.0-alpha";
        $this->config = $config;

        parent::__construct($name, $version);
    }

    /**
     * @return \Phpteda\CLI\Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * {@inheritDoc}
     */
    public function doRun(InputInterface $input, OutputInterface $output)
    {
        $this->io = new ConsoleIO($input, $output, $this->getHelperSet());

        return parent::doRun($input, $output);
    }

    /**
     * @return ConsoleIO
     */
    public function getIO()
    {
        return $this->io;
    }

    /**
     * @return int|null
     */
    public function getTerminalWidth()
    {
        $dimensions = $this->getTerminalDimensions();
        return $dimensions[0];
    }

    /**
     * Defines and returns default input definiton
     *
     * @return InputDefinition
     */
    protected function getDefaultInputDefinition()
    {
        return new InputDefinition(
            array(
                new InputArgument('command', InputArgument::REQUIRED, 'The command to execute'),
                new InputOption('--help', '-h', InputOption::VALUE_NONE, 'Display this help message.'),
                new InputOption('--verbose', '-v', InputOption::VALUE_NONE, 'Increase verbosity of messages.'),
                new InputOption('--version', '-V', InputOption::VALUE_NONE, 'Display this application version.'),
            )
        );
    }
}
