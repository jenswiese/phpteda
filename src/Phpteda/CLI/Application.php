<?php

namespace Phpteda\CLI;

use Symfony\Component\Console\Application as SymfonyApplication;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Command\HelpCommand;
use Phpteda\CLI\Config;

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

    /**
     * Constructor of the class
     */
    public function __construct(Config $config)
    {
        $name = "Phpteda";
        $version = "0.1-dev";
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
