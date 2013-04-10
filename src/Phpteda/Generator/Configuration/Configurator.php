<?php

namespace Phpteda\Generator\Configuration;

use Phpteda\Generator\GeneratorInterface;
use InvalidArgumentException;
use Phpteda\Reflection\ReflectionClass;
use Phpteda\Reflection\MethodRetriever;
use RuntimeException;
use Faker\Factory as FakerFactory;

/**
 * Class for configure a given generator
 *
 * @author Jens Wiese <jens@howtrueisfalse.de>
 * @since 2013-03-19
 */
class Configurator
{
    /** @var array */
    protected $properties = array();

    /** @var string */
    protected $generatorClassName;

    /** @var GeneratorInterface */
    protected $configuredGenerator;

    /**
     * @param string $generatorClassName fully qualified name
     * @return Configurator
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
     * @return Configurator
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
        $this->properties = $this->retrievePropertiesFromGenerator();
    }

    /**
     * @return string
     */
    public function getGeneratorClassName()
    {
        return $this->generatorClassName;
    }


    /**
     * @param array $properties
     */
    public function fromArray(array $properties)
    {
        foreach ($properties as $propertyData) {
            $property = new Property();
            $property->fromArray($propertyData);
            $this->properties[] = $property;
        }
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $propertyArray = array();

        foreach ($this->properties as $property) {
            $propertyArray[] = $property->toArray();
        }

        return $propertyArray;
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
     * @return ConfiguratorProperty[]
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * @param array $properties
     */
    public function setProperties(array $properties)
    {
        $this->properties = $properties;
    }

    /**
     * Retrieves the properties from the generator by
     * reflection
     *
     * @return ConfiguratorProperty[]
     */
    protected function retrievePropertiesFromGenerator()
    {
        $properties = array_merge(
            $this->getGroupedProperties(),
            $this->getSelectionProperties()
        );

        return $properties;
    }

    /**
     * Configures generator by given properties
     */
    protected function configureGenerator()
    {
        $this->configuredGenerator = call_user_func(
            array($this->generatorClassName, 'generate')
        );

        foreach ($this->properties as $property) {
            if ($property instanceof PropertyGroup) {
                foreach ($property->getProperties() as $groupedProperty) {
                    $this->configuredGenerator->{$groupedProperty->getName()}($groupedProperty->getValue());
                }
            } elseif ($property instanceof PropertySelection) {
                $selectedProperty = $property->getSelectedProperty();

                if ($selectedProperty instanceof Property) {
                    $this->configuredGenerator->{$selectedProperty->getName()}($selectedProperty->getValue());
                }
            }
        }
    }

    /**
     * @return PropertyGroup[]
     */
    protected function getGroupedProperties()
    {
        $groups = array();

        $reflectionClass = new ReflectionClass($this->generatorClassName);
        $annotationReader = $reflectionClass->getAnnotationReader();

        foreach ($annotationReader->getGroupedMethods() as $name => $methods) {
            $group = new PropertyGroup($name);
            $group->fromMethodArray($methods);
            $groups[] = $group;
        }

        return $groups;
    }

    /**
     * @return PropertySelection[]
     */
    protected function getSelectionProperties()
    {
        $selections = array();

        $reflectionClass = new ReflectionClass($this->generatorClassName);
        $annotationReader = $reflectionClass->getAnnotationReader();

        foreach ($annotationReader->getSelectableMethods() as $name => $methods) {
            $selection = new PropertySelection($name);
            $selection->fromMethodArray($methods);
            $selections[] = $selection;
        }

        return $selections;
    }
}