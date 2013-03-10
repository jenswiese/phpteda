<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once 'UserGenerator.php';
require_once 'User.php';

$faker = Faker\Factory::create('de_DE');
$generator = new UserGenerator($faker);

$generator
    ->generate()
    ->activeUser()
    ->createdAtToday()
    ->shouldRemoveExistingData()
    ->amount(10);

$generator
    ->generate()
    ->activeUser()
    ->noEmail()
    ->amount(20);


