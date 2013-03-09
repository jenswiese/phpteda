<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once 'UserGenerator.php';
require_once 'User.php';

$generator = new UserGenerator(Faker\Factory::create('de_DE'));

$generator
    ->activeUser()
    ->createdAtToday()
    ->shouldRemoveExistingData()
    ->amount(10);

$generator
    ->activeUser()
    ->noEmail()
    ->amount(20);


