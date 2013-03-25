<?php

namespace Phpteda\Generator;

use Faker\Factory;
use Faker\Generator;
use Phpteda\Reflection\ReflectionClass;
use Zend\File\Transfer\Exception\InvalidArgumentException;

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
     * @param Generator $faker
     * @return AbstractGenerator
     */
    public static function generate(Generator $faker = null)
    {
        return new static($faker);
    }

    /**
     * @param Generator $faker
     * @throws \InvalidArgumentException
     */
    protected function __construct(Generator $faker = null)
    {
        if (is_null($faker)) {
            $faker = Factory::create('de_DE');
        }

        foreach ($this->getProvidersByAnnotation() as $providerClass) {
            if (!class_exists($providerClass)) {
                throw new \InvalidArgumentException("Provider '" . $providerClass . "' does not exist.");
            }

            $faker->addProvider(new $providerClass($faker));
        }

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
     * @return $this|GeneratorInterface
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
     * @return Generator
     */
    public function getFaker()
    {
        return $this->faker;
    }

    /**
     * @return string[]
     */
    protected function getProvidersByAnnotation()
    {
        $classReader = new ReflectionClass(get_called_class());
        return $classReader->getAnnotations('fakerProvider');
    }

    /**
     * @return string
     */
    protected function getLocaleByAnnotation()
    {
        $classReader = new ReflectionClass(get_called_class());
        $annotatedLocale = $classReader->getAnnotations('fakerLocale');
        $locale = empty($annotatedLocale) ? 'en_EN' : $annotatedLocale;

        return $locale;
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
