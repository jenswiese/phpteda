<?php

namespace Phpteda\Generator;

use Phpteda\Generator\GeneratorConfig;
use Phpteda\Generator\Configuration\PropertyGroup;
use Phpteda\Generator\GeneratorInterface;
use InvalidArgumentException;
use Phpteda\Reflection\ReflectionClass;
use RuntimeException;
use Faker\Factory as FakerFactory;

/**
 * Class for build up given generator
 *
 * @author Jens Wiese <jens@howtrueisfalse.de>
 * @since 2013-03-19
 */
class GeneratorBuilder
{
    /** @var GeneratorConfig */
    protected $generatorConfig;

    /** @var array */
    protected $propertyGroups = array();

    /** @var string */
    protected $generatorClassName;

    /** @var GeneratorInterface */
    protected $configuredGenerator;

    /**
     * @param string $generatorClassName fully qualified name
     * @return GeneratorBuilder
     * @throws \RuntimeException
     */
    public static function createByGeneratorClassName($generatorClassName)
    {
        if (!class_exists($generatorClassName)) {
            throw new RuntimeException(
                "Class '" . $generatorClassName . "' does not exists. Please verify the autoloading config."
            );
        }

        return new self($generatorClassName);
    }

    /**
     * Expects pathname of generator-file,
     * and extracts namespace and classname out of the file
     *
     * @param $pathname
     * @return GeneratorBuilder
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public static function createByGeneratorPathname($pathname)
    {
        $classReader = ReflectionClass::createByPathname($pathname);

        return self::createByGeneratorClassName($classReader->getName());
    }

    /**
     * @param string fully qualified class-name
     */
    private function __construct($generatorClassName)
    {
        $this->generatorClassName = $generatorClassName;
        $this->generatorConfig = new GeneratorConfig();
        $this->propertyGroups = $this->retrievePropertiesFromGenerator();
    }

    /**
     * @param GeneratorConfig $generatorConfig
     */
    public function setGeneratorConfig(GeneratorConfig $generatorConfig)
    {
        $this->generatorConfig = $generatorConfig;
    }

    /**
     * @return string
     */
    public function getGeneratorClassName()
    {
        return $this->generatorClassName;
    }

    /**
     * @return GeneratorInterface
     */
    public function getConfiguredGenerator()
    {
        $this->configureGenerator();

        return $this->configuredGenerator;
    }

    /**
     * @return PropertyGroup[]
     */
    public function getPropertyGroups()
    {
        return $this->propertyGroups;
    }

    /**
     * @return Property[]
     */
    public function getProperties()
    {
        $properties = array();
        foreach ($this->getPropertyGroups() as $group) {
            $properties = array_merge($properties, $group->getProperties());
        }

        return $properties;
    }

    /**
     * Retrieves the properties from the generator by
     * reflection
     *
     * @return ConfiguratorProperty[]
     */
    protected function retrievePropertiesFromGenerator()
    {
        $xmlConfig = call_user_func(
            array($this->generatorClassName, 'getConfig')
        );

        return $this->generatorConfig->readFromXml($xmlConfig)->getPropertyGroups();
    }

    /**
     * Configures generator by given properties
     */
    protected function configureGenerator()
    {
        $this->configuredGenerator = call_user_func(
            array($this->generatorClassName, 'generate')
        );

        foreach ($this->getProperties() as $property) {
            $this->configuredGenerator->{$property->getName()}($property->getValue());
        }
    }
}
