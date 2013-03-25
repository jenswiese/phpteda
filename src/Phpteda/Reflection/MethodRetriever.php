<?php

namespace Phpteda\Reflection;

use Phpteda\Reflection\ReflectionClass;

/**
 * Class for retrieving methods from given Generator class
 *
 * @author jens
 * @since 2013-03-11
 */
class MethodRetriever
{
    /** @var \Phpteda\Reflection\ReflectionClass */
    protected $reflectionClass;

    protected static $ignoredMethodNames = array(
        '__call', 'generate', '__get', 'amount', 'shouldRemoveExistingData'
    );

    /**
     * @param ReflectionClass $reflectionClass
     */
    public function __construct(ReflectionClass $reflectionClass)
    {
        $this->reflectionClass = $reflectionClass;
    }

    /**
     * @return Method[]
     */
    public function getAllPublicMethods()
    {
        $publicMethods = $this->getMethods(\ReflectionMethod::IS_PUBLIC);
        $magicMethods = $this->getMagicMethods();

        return array_merge($magicMethods, $publicMethods);
    }

    /**
     * @param int $filter
     * @return Method[]
     */
    public function getMethods($filter)
    {
        $methods = array();

        foreach ($this->reflectionClass->getMethods($filter) as $reflectionMethod) {
            if ($this->isIgnoredMethodName($reflectionMethod->getName())) {
                continue;
            }

            $method = new Method();
            $method->setName($reflectionMethod->getName());
            $parameter = $reflectionMethod->getParameters();
            if (isset($parameter[0])) {
                $method->setParameterName($parameter[0]);
            }
            $methods[] = $method;
        }

        return $methods;
    }

    /**
     * @return Method[]
     */
    public function getMagicMethods()
    {
        $methods = array();

        $magicMethodAnnotations = $this->reflectionClass->getAnnotations('method');

        foreach ($magicMethodAnnotations as $methodString) {
            $method = new Method(
                $this->reflectionClass->parseMagicMethodAnnotation($methodString)
            );

            if ($this->isIgnoredMethodName($method->getName())) {
                continue;
            }

            $methods[] = $method;
        }

        return $methods;
    }

    /**
     * @param $name
     * @return bool
     */
    private function isIgnoredMethodName($name)
    {
        return in_array($name, self::$ignoredMethodNames);
    }
}
