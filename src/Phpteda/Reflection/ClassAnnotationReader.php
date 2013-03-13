<?php

namespace Phpteda\Reflection;

use ReflectionClass;
use OutOfBoundsException;

/**
 * Class for ...
 *
 * @author jens
 * @since 2013-03-11
 */
class ClassAnnotationReader
{
    /** @var ReflectionClass */
    protected $reflectionClass;

    /**
     * @param \ReflectionClass $reflectionClass
     */
    public function __construct(ReflectionClass $reflectionClass = null)
    {
        $this->reflectionClass = $reflectionClass;
    }

    /**
     * @param \ReflectionClass $reflectionClass
     * @return ClassAnnotationReader
     */
    public function setReflectionClass($reflectionClass)
    {
        $this->reflectionClass = $reflectionClass;

        return $this;
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

        $pattern = is_null($annotation) ? "/\@([a-zA-Z1-9]*) (.*)/" : "/\@(" . $annotation . ") (.*)/";
        preg_match_all(
            $pattern,
            $this->reflectionClass->getDocComment(),
            $matches,
            PREG_SET_ORDER
        );

        $annotations = array();
        foreach($matches as $match) {
            $name = $match[1];
            $value = $match[2];

            $annotations[$name][] = $value;
         }

        if (!is_null($annotation)) {
            $annotations = $annotations[$annotation];
        }

        return $annotations;
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
        $returnTypePattern = "^(.*\ )?";
        $parameterPattern = "(.*\ )?(.*)";
        $methodPattern = "(([a-zA-Z1-9]*)\(" . $parameterPattern . "\))";
        $descriptionPattern = "(.*)?";
        $pattern = "/" . $returnTypePattern . $methodPattern . $descriptionPattern . "/";

        preg_match_all($pattern, $methodString, $matches, PREG_SET_ORDER);

        $returnType = trim($matches[0][1]);
        $methodName = trim($matches[0][3]);
        $parameterType = trim($matches[0][4]);
        $parameterName = trim(str_replace('$', '', $matches[0][5]));
        $description = trim($matches[0][6]);

        return array(
            'returnType' => $returnType,
            'name' => $methodName,
            'parameterType' => $parameterType,
            'parameterName' => $parameterName,
            'description' => $description
        );
    }
}
