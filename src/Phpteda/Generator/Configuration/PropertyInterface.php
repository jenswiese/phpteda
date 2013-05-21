<?php
/**
 * Class for ...
 *
 * @author Jens Wiese <jens@howtrueisfalse.de>
 * @since 2013-05-10
 */

namespace Phpteda\Generator\Configuration;

interface PropertyInterface
{
    /**
     * @param string $name
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $question
     */
    public function setQuestion($question);

    /**
     * @return string
     */
    public function getQuestion();

    /**
     * @param mixed $value
     */
    public function setValue($value);

    /**
     * @return mixed
     */
    public function getValue();
}
