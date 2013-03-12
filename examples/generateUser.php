<?php

require_once __DIR__ . '/../vendor/autoload.php';
//require_once 'UserGenerator.php';
//require_once 'User.php';

$faker = Faker\Factory::create('de_DE');

UserGenerator::generate($faker)
    ->activeUser()
    ->createdAtToday()
    ->shouldRemoveExistingData()
    ->amount(20);

UserGenerator::generate($faker)
    ->activeUser()
    ->noEmail()
    ->withUserCategory(1234)
    ->amount(20);