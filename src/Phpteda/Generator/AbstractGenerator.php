<?php

namespace Phpteda\Generator;

use Phpteda\TestData\Factory as TestDataFactory;
use Phpteda\TestData\Generator as TestDataGenerator;
use Phpteda\Reflection\ReflectionClass;

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

    /** @var TestDataGenerator */
    protected $testDataGenerator;

    /** @var bool */
    protected $shouldRemoveExistingData = false;

    /**
     * @param TestDataGenerator $testDataGenerator
     * @return $this
     */
    public static function generate(TestDataGenerator $testDataGenerator = null)
    {
        return new static($testDataGenerator);
    }

    /**
     * @param TestDataGenerator $testDataGenerator
     * @throws \InvalidArgumentException
     */
    protected function __construct(TestDataGenerator $testDataGenerator = null)
    {
        if (is_null($testDataGenerator)) {
            $testDataGenerator = TestDataFactory::create($this->getLocale());
        }

        foreach ($this->getProviders() as $providerClass) {
            if (!class_exists($providerClass)) {
                throw new \InvalidArgumentException("Provider '" . $providerClass . "' does not exist.");
            }

            $testDataGenerator->addProvider(new $providerClass($testDataGenerator));
        }

        $this->testDataGenerator = $testDataGenerator;
        $this->options = new Options();
    }

    /**
     * Returns XML-config in order to configure the generator
     *
     * @return string XML-config for GeneratorBuilder
     */
    public static function getConfig()
    {
        return '<config></config>';
    }

    /**
     * Returns Locale of testdata generation (e.g. de_DE)
     *
     * @return string Locale
     */
    public function getLocale()
    {
        return 'de_DE';
    }

    /**
     * Returns providers for TestDataGenerator, override this method in order to
     * define specific providers
     *
     * @return array of TestDataGenerator providers
     */
    public function getProviders()
    {
        return array();
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
        $this->setupGeneration();

        if ($this->shouldRemoveExistingData) {
            $this->removeExistingData();
        }

        $this->preGenerateData();

        for ($i = 0; $i < $amount; $i++) {
            $this->generateData();
        }

        $this->postGenerateData();

        $this->terminateGeneration();

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
     * @return TestDataGenerator
     */
    public function getTestDataGenerator()
    {
        return $this->testDataGenerator;
    }

    /**
     * @return string[]
     */
    protected function getProvidersByConfig()
    {
        $reader = new XMLConfigurationReader();

        return $reader->getProviders();
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
     * Implement custom behaviour for setting up generation
     */
    protected function setupGeneration()
    {
    }

    /**
     * Implement custom behaviour prior to generation (e.g. header of file)
     */
    protected function preGenerateData()
    {
    }

    /**
     * Implement custom behaviour after to generation (e.g. header of file)
     */
    protected function postGenerateData()
    {
    }

    /**
     * Implement custom behaviour for terminating generation
     */
    protected function terminateGeneration()
    {
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
