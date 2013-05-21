<?php

namespace Phpteda\Generator;

use Phpteda\TestData\Generator as TestDataGenerator;

/**
 * Interface for Generator
 *
 * @author Jens Wiese <jens@howtrueisfalse.de>
 * @since 2013-03-19
 */
interface GeneratorInterface
{
    /**
     * @param TestDataGenerator $generator
     * @return GeneratorInterface
     */
    public static function generate(TestDataGenerator $generator);

    /**
     * Returns XML-config in order to configure the generator
     *
     * @return string XML-config for GeneratorBuilder
     */
    public static function getConfig();


    /**
     * @return GeneratorInterface
     */
    public function shouldRemoveExistingData();

    /**
     * @param integer $amount
     * @return void
     */
    public function amount($amount);
}