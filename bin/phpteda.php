#!/usr/bin/php
<?php

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

use Phpteda\CLI\Application;
use Phpteda\CLI\Config;
use Phpteda\CLI\Command\InitCommand;
use Phpteda\CLI\Command\ShowCommand;

$application = new Application(new Config(getcwd()));
$application->add(new InitCommand());
$application->add(new ShowCommand());
$application->run();