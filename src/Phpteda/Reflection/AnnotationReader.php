<?php

namespace Phpteda\Reflection;

use Phpteda\Reflection\Method\Method;

/**
 * Class AnnotationReader
 *
 * @author Jens Wiese <jens@howtrueisfalse.de>
 * @since 2013-03-25
 */
class AnnotationReader
{
    /** @var string */
    protected $docComment;

    /**
     * @param string $docComment
     */
    public function __construct($docComment)
    {
        $this->docComment = $docComment;
    }

    /**
     * @param $docComment
     * @return AnnotationReader
     */
    public static function create($docComment)
    {
        return new self($docComment);
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        $pattern = "/\/[\*]{2}([.\n\s\])*([a-zA-Z1-9,\.\?\!\s]*)([\n\s]*)\@/";
        preg_match_all(
            $pattern,
            $this->docComment,
            $matches,
            PREG_SET_ORDER
        );

        $description = isset($matches[0][1]) ? $matches[0][1] : '';

        if (!empty($description)) {
            $description = str_replace(array('*'), '', $description);
            $lines = explode(PHP_EOL, $description);
            $lines = array_map(
                function ($value) {
                    $value = trim($value);
                    return !empty($value) ? $value : false;
                },
                $lines
            );

            $description = trim(implode(' ', $lines));
            return $description;
        }

        return $description;
    }

    /**
     * @param $annotation
     * @return array
     */
    public function getAnnotations($annotation = null)
    {
        $pattern = is_null($annotation) ? "/\@([a-zA-Z1-9]*) (.*)/" : "/\@(" . $annotation . ") (.*)/";
        preg_match_all(
            $pattern,
            $this->docComment,
            $matches,
            PREG_SET_ORDER
        );

        $annotations = array();
        foreach ($matches as $match) {
            $name = $match[1];
            $value = $match[2];

            $nameExistsAlready = isset($annotations[$name]);

            if ($nameExistsAlready) {
                if (!is_array($annotations[$name])) {
                    $annotations[$name] = array($annotations[$name]);
                }

                $annotations[$name][] = $value;
            } else {
                $annotations[$name] = $value;
            }
        }

        if (!is_null($annotation) && isset($annotations[$annotation])) {
            $annotations = $annotations[$annotation];
        }

        return $annotations;
    }

    /**
     * @return Method[]
     */
    public function getSelectableMethods()
    {
        $methods = array();

        foreach ($this->getSelectableMethodAnnotations() as $name => $annotations) {
            foreach ($annotations as $methodString) {
                $methods[$name][] = new Method($this->parseMagicMethodAnnotation($methodString));
            }
        }

        return $methods;
    }

    /**
     * @return Method[]
     */
    public function getGroupedMethods()
    {
        foreach ($this->getUntaggedMethodAnnotations() as $methodString) {
            $methods['Common'][] = new Method($this->parseMagicMethodAnnotation($methodString));
        }

        foreach ($this->getGroupedMethodAnnotations() as $name => $annotations) {
            foreach ($annotations as $methodString) {
                $methods[$name][] = new Method($this->parseMagicMethodAnnotation($methodString));
            }
        }

        return $methods;
    }

    /**
     * @return array
     */
    public function getSelectableMethodAnnotations()
    {
        return $this->getAnnotationsByTagName('select', 'method');
    }

    /**
     * @return array
     */
    public function getGroupedMethodAnnotations()
    {
        return $this->getAnnotationsByTagName('group', 'method');
    }

    /**
     * @return array
     */
    public function getUntaggedMethodAnnotations()
    {
        $docComment = $this->docComment;
        $removableTags = array('select', 'group');

        foreach ($removableTags as $tag) {
            $pattern = '#<' . $tag . '(?:\s+[^>]+)?>(.*?)</' . $tag . '>#s';
            $docComment = preg_replace($pattern, '', $docComment);
        }

        $reader = new AnnotationReader($docComment);
        return $reader->getAnnotations('method');
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

    /**
     * @param $tagName
     * @param null $annotation
     * @return array
     */
    protected function getAnnotationsByTagName($tagName, $annotation = null)
    {
        $annotations = array();

        $pattern = '#<'.$tagName.'(?:\s+[^>]+)?>(.*?)</'.$tagName.'>#s';
        preg_match_all(
            $pattern,
            $this->docComment,
            $matches,
            PREG_SET_ORDER
        );

        foreach ($matches as $match) {
            $xml = new \SimpleXMLElement($match[0]);
            $annotationReader = new AnnotationReader(trim($xml));
            $name = trim($xml->attributes()->name);
            $annotations[$name] = $annotationReader->getAnnotations($annotation);
        }

        return $annotations;
    }
}