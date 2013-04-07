<?php

namespace Phpteda\Reflection;

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
    /** @var \ReflectionClass */
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
     * @return AnnotationReader
     */
    public function getAnnotationReader()
    {
        return $this->annotationReader;
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

        if (!isset($className)) {
            throw new \RuntimeException("File '" . $pathname . "' does not contain class-name.");
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
}
