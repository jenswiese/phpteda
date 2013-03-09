<?php

namespace Phpteda\Generator;

use Phpteda\FakeData\FakeData;

/**
 * Abstract base class for custom generator classes
 *
 * @author Jens Wiese <jens@howtrueisfalse.de>
 * @since 2013-03-07
 */
abstract class GeneratorBase
{
    /** @var int */
    protected $quantity;

    /** @var Configuration */
    protected $config;

    /** @var Faker */
    protected $fakeData;


    /**
     * Creates Generator and defines quantity
     *
     * @param int $quantity
     * @return self
     */
    public static function generate($quantity = 1)
    {
        return new static($quantity);
    }

    /**
     * @param $quantity
     */
    protected function __construct($quantity)
    {
        $this->quantity = $quantity;
        $this->config = new Configuration();
    }

    /**
     * @question 'Should existing jobs be deleted?'
     * @return GeneratorBase
     */
    public function removeExistingData()
    {
        $this->config->setBooleanOption(__METHOD__);

        return $this;
    }

    /**
     * Executes test data generation and terminates method chain
     *
     * @param \Phpteda\FakeData\FakeData $fakeData
     * @return void
     */
    public function execute()
    {
        for ($i = 0; $i < $this->quantity; $i++) {
            $this->generateData();
        }
    }

    /**
     * Implements custom generator behaviour
     *
     * @return void
     */
    abstract protected function generateData();
}
