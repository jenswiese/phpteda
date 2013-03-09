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
abstract class AbstractGenerator
{
    /** @var Options */
    protected $options;

    /** @var \Faker\Generator */
    protected $faker;

    /** @var bool */
    protected $shouldRemoveExistingData = false;

    /**
     * @param \Faker\Generator $faker
     */
    public function __construct(Generator $faker)
    {
        $this->faker = $faker;
        $this->options = new Options();
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed|AbstractGenerator
     */
    public function __call($name, $arguments)
    {
        if (empty($arguments)) {
            $this->options->setBooleanOption($name);
        } else {
            $this->options->setOption($name, $arguments);
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
     * @param bool $condition
     * @param mixed $choiceOne
     * @param mixed $choiceTwo
     * @return mixed
     */
    protected function chooseIf($condition, $choiceOne, $choiceTwo)
    {
        return $condition ? $choiceOne : $choiceTwo;
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
     * @return AbstractGenerator
     */
    abstract protected function removeExistingData();

    /**
     * Implements custom generator behaviour
     *
     * @return void
     */
    abstract protected function generateData();
}
