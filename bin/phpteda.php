#!/usr/bin/php
<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Phpteda\CLI\Application;
use Phpteda\CLI\Config;
use Phpteda\CLI\Command\InitCommand;

$application = new Application(new Config(getcwd()));
$application->add(new InitCommand());
$application->run();