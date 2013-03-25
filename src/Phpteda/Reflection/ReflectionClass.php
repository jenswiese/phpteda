<?php

namespace Phpteda\Reflection;

use OutOfBoundsException;
use InvalidArgumentException;
use RuntimeException;

/**
 * Class for retrieving infos about class (e.g. annotations, description)
 *
 * @author jens
 * @since 2013-03-11
 */
class ReflectionClass
{
    /** @var ReflectionClass */
    protected $reflectionClass;

    /** @var AnnotationReader */
    protected $annotationReader;

    /**
     * @param $className
     */
    public function __construct($className)
    {
        $this->reflectionClass = new \ReflectionClass($className);
        $this->annotationReader = new AnnotationReader($this->reflectionClass->getDocComment());

    }

    /**
     * @param AnnotationReader $reader
     */
    public function setAnnotationReader(AnnotationReader $reader)
    {
        $this->annotationReader = $reader;
    }

    /**
     * @param $pathname
     * @return ReflectionClass
     * @throws RuntimeException
     * @throws InvalidArgumentException
     */
    public static function createByPathname($pathname)
    {
        if (!file_exists($pathname)) {
            throw new InvalidArgumentException("Pathname '" . $pathname . "' does not exist.");
        }

        $fileContent = file_get_contents($pathname);

        $namespacePattern = '/namespace(\s*)([a-zA-Z\\\\]*)/';
        preg_match($namespacePattern, $fileContent, $matches);
        if (isset($matches[2])) {
            $namespace = trim(trim($matches[2]), '\\');
        }

        $classPattern = '/class(\s*)([a-zA-Z]*)/';
        preg_match($classPattern, $fileContent, $matches);
        if (isset($matches[2])) {
            $className = trim($matches[2]);
        }

        if (!isset($namespace) || !isset($className)) {
            throw new \RuntimeException("File '" . $pathname . "' does not contain namespace or class-name.");
        }

        return new self($namespace . '\\' .  $className);
    }

    /**
     * @param string $filter (e.g. ReflectionMethod::IS_PUBLIC)
     * @return ReflectionMethod[]
     */
    public function getMethods($filter = null)
    {
        return $this->reflectionClass->getMethods($filter);
    }

    /**
     * Returns fully qualified name
     *
     * @return string
     */
    public function getNamespaceName()
    {
        return $this->reflectionClass->getNamespaceName();
    }

    /**
     * Returns class-name
     *
     * @return string
     */
    public function getName()
    {
        return $this->reflectionClass->getName();
    }

    /**
     * Retrieves the description of class
     *
     * @return string
     * @throws OutOfBoundsException
     */
    public function getDescription()
    {
        if (is_null($this->reflectionClass)) {
            throw new OutOfBoundsException('ReflectionClass is not set.');
        }

        return $this->annotationReader->getDescription(
            $this->reflectionClass->getDocComment()
        );
    }

    /**
     * @param string|null $annotation
     * @return array|mixed
     * @throws \OutOfBoundsException
     */
    public function getAnnotations($annotation = null)
    {
        if (is_null($this->reflectionClass)) {
            throw new OutOfBoundsException('ReflectionClass is not set.');
        }

        return $this->annotationReader->getAnnotations(
            $this->reflectionClass->getDocComment(),
            $annotation
        );
    }

    /**
     * Returns infos about magic method as array
     *
     * Example:
     *
     *      array(
     *          'returnType' => 'UserGenerator',
     *          'methodName' => 'setName',
     *          'parameterType' => 'string',
     *          'parameterName' => 'name',
     *          'description' => 'Please provide name.'
     *      );
     *
     * @param string $methodString
     * @return array
     */
    public function parseMagicMethodAnnotation($methodString)
    {
        $this->annotationReader->parseMagicMethodAnnotation($methodString);
    }
}
