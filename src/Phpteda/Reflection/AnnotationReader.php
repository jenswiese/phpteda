<?php

namespace Phpteda\Reflection;

/**
 * Class AnnotationReader
 *
 * @author Jens Wiese <jens@howtrueisfalse.de>
 * @since 2013-03-25
 */
class AnnotationReader
{
    /**
     * @param string $docComment
     * @return string
     */
    public function getDescription($docComment)
    {
        $pattern = "/\/[\*]{2}([.\n\s\])*([a-zA-Z1-9,\.\?\!\s]*)([\n\s]*)\@/";
        preg_match_all(
            $pattern,
            $docComment,
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
     * @param string $docComment
     * @param $annotation
     * @return array
     */
    public function getAnnotations($docComment, $annotation = null)
    {
        $pattern = is_null($annotation) ? "/\@([a-zA-Z1-9]*) (.*)/" : "/\@(" . $annotation . ") (.*)/";
        preg_match_all(
            $pattern,
            $docComment,
            $matches,
            PREG_SET_ORDER
        );

        $annotations = array();
        foreach ($matches as $match) {
            $name = $match[1];
            $value = $match[2];

            $annotations[$name][] = $value;
        }

        if (!is_null($annotation) && isset($annotations[$annotation])) {
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