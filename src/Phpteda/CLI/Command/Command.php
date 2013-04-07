<?php

namespace Phpteda\CLI\Command;

use Phpteda\CLI\Application;
use Phpteda\CLI\IO\ConsoleIO;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

/**
 * Class Command
 *
 * @author jens
 * @since 2013-04-07
 */
abstract class Command extends SymfonyCommand
{
    /**
     * @return ConsoleIO
     */
    public function getIO()
    {
        return $this->getApplication()->getIO();
    }

    /**
     * @return \Phpteda\CLI\Config
     */
    public function getConfig()
    {
        return $this->getApplication()->getConfig();
    }

    /**
     * @return Application
     */
    public function getApplication()
    {
        return parent::getApplication();
    }
}