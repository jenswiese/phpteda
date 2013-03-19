<?php

namespace Phpteda\Generator;

use Faker\Generator;

/**
 * Interface for Generator
 *
 * @author Jens Wiese <jens@howtrueisfalse.de>
 * @since 2013-03-19
 */
interface GeneratorInterface
{
    /**
     * @param Generator $generator
     * @return GeneratorInterface
     */
    public static function generate(Generator $generator);

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