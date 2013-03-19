<?php

namespace Phpteda\Generator;

use Faker\Factory;
use Faker\Generator;

/**
 * Abstract base class for custom generator classes
 *
 * @author Jens Wiese <jens@howtrueisfalse.de>
 * @since 2013-03-07
 */
abstract class AbstractGenerator implements GeneratorInterface
{
    /** @var Options */
    protected $options;

    /** @var \Faker\Generator */
    protected $faker;

    /** @var bool */
    protected $shouldRemoveExistingData = false;

    /**
     * @param \Faker\Generator $faker
     * @return AbstractGenerator
     */
    public static function generate(Generator $faker)
    {
        return new static($faker);
    }

    /**
     * @param \Faker\Generator $faker
     */
    protected function __construct(Generator $faker)
    {
        $this->faker = $faker;
        $this->options = new Options();
    }

    /**
     * @param $name
     * @param $arguments
     *
     * @return AbstractGenerator
     */
    public function __call($name, $arguments)
    {
        if (empty($arguments)) {
            $this->options->setBooleanOption($name);
        } else {
            $this->options->setOption($name, $arguments[0]);
        }

        return $this;
    }

    /**
     * Enables removing of existing data
     * @return AbstractGenerator
     */
    public function shouldRemoveExistingData()
    {
        $this->shouldRemoveExistingData = true;

        return $this;
    }

    /**
     * Executes test data generation and terminates method chain
     *
     * @param int $amount
     * @return void
     */
    public function amount($amount)
    {
        if ($this->shouldRemoveExistingData) {
            $this->removeExistingData();
        }

        for ($i = 0; $i < $amount; $i++) {
            $this->generateData();
        }

        $this->reset();
    }

    /**
     * @param $name
     * @return bool|mixed
     */
    public function __get($name)
    {
        if ($this->options->hasOption($name)) {
            return $this->options->getOption($name);
        }

        return false;
    }

    /**
     * Resets current generation process
     */
    protected function reset()
    {
        $this->options = new Options();
        $this->shouldRemoveExistingData = false;
    }

    /**
     * Implements custom way to delete existing data
     *
     * @return void
     */
    abstract protected function removeExistingData();

    /**
     * Implements custom generator behaviour
     *
     * @return void
     */
    abstract protected function generateData();
}
