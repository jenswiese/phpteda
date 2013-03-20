<?php

namespace Phpteda\Generator\Configuration;

use Phpteda\Generator\GeneratorInterface;
use InvalidArgumentException;
use Phpteda\Reflection\ClassAnnotationReader;
use Phpteda\Reflection\MethodRetriever;
use RuntimeException;
use ReflectionClass;
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
        if (!file_exists($pathname)) {
            throw new InvalidArgumentException("Generator pathname '" . $pathname . "' does not exist.");
        }

        $fileContent = file_get_contents($pathname);
        foreach (token_get_all($fileContent) as $token) {
            if (T_NAMESPACE == $token[0]) {
                $namespace = $token[1];
            }

            if (T_CLASS == $token[0]) {
                $className = $token[1];
                break;
            }
        }

        if (is_null($namespace) || is_null($className)) {
            throw new RuntimeException("File does not contain namespace or class-name.");
        }

        return self::createByGeneratorClassName($namespace . '\\' .  $className);
    }

    /**
     * @param string fully qualified class name or pathname
     */
    private function __construct($generatorClassName)
    {
        $this->generatorClassName = $generatorClassName;
        $this->properties = $this->retrievePropertiesFromGenerator();
    }


    /**
     * @param array $properties
     */
    public function fromArray(array $properties)
    {
        foreach ($properties as $propertyData) {
            $property = new ConfiguratorProperty();
            $property->fromArray($propertyData);
            $this->properties[$propertyData['name']] = $property;
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

        return $this->generatorClassName;
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
        $properties = array();

        $methodRetriever = new MethodRetriever(
            new ReflectionClass($this->generatorClassName),
            new ClassAnnotationReader()
        );

        foreach ($methodRetriever->getAllPublicMethods() as $method) {
            $property = new ConfiguratorProperty();
            $property->setName($method->getName());
            $property->setQuestion($method->getDescription());

            $type = $method->hasParameter() ? ConfiguratorProperty::TYPE_MIXED : ConfiguratorProperty::TYPE_BOOL;
            $property->setType($type);

            $properties[$method->getName()] = $property;
        }

        return $properties;
    }

    /**
     * Configures generator by given properties
     */
    protected function configureGenerator()
    {
        $this->configuredGenerator = call_user_func(
            array($this->generatorClassName, 'generate'),
            FakerFactory::create()
        );

        foreach ($this->properties as $property) {
            $this->configuredGenerator->{$property->getName()}($property->getValue());
        }
    }
}