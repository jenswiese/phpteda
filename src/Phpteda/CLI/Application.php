<?php

namespace Phpteda\CLI;

use Symfony\Component\Console\Application as SymfonyApplication;
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
}
