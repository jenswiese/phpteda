<?php

namespace Phpteda\Util;

/**
 * Evaluator class that provides control structures
 * via a fluent interface
 *
 * @author Jens Wiese <jens@howtrueisfalse.de>
 * @since 2013-03-10
 */
class Evaluator
{
    /** @var bool */
    protected $whenValue = false;

    /** @var mixed */
    protected $firstConditionalValue;

    /**
     * @param bool $condition
     * @return Evaluator
     */
    public static function when($condition)
    {
        return new self($condition);
    }

    /**
     * @param bool $condition
     */
    private function __construct($condition)
    {
        $this->whenValue = $condition;
    }

    /**
     * @param mixed $value
     * @return Evaluator
     */
    public function then($value)
    {
        $this->firstConditionalValue = $value;
        return $this;
    }

    /**
     * @param $value
     * @return mixed | null
     */
    public function thenReturn($value)
    {
        return $this->whenValue ? $value : null;
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    public function otherwise($value)
    {
        if (is_null($this->firstConditionalValue)) {
            return $this->whenValue ? $this->whenValue : $value;
        } else {
            return $this->whenValue ? $this->firstConditionalValue : $value;
        }
    }
}
