#!/usr/bin/php
<?php

use Phpteda\CLI\Application;
use Phpteda\CLI\Config;
use Phpteda\CLI\Command\InitCommand;
use Phpteda\CLI\Command\ShowCommand;
use Phpteda\CLI\Command\GenerateCommand;

$files = array(
    __DIR__ . '/../vendor/autoload.php',
    __DIR__ . '/../../../autoload.php'
);

foreach ($files as $file) {
    if (file_exists($file)) {
        require_once $file;
        break;
    }
}

$configuration = new Config(getcwd());

if ($configuration->hasBootstrapPathname()) {
    require_once $configuration->getBootstrapPathname();
}

$application = new Application($configuration);
$application->add(new InitCommand());
$application->add(new ShowCommand());
$application->add(new GenerateCommand());
$application->run();