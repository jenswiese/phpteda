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
}