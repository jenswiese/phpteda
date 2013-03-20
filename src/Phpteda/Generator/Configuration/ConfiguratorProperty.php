<?php

namespace Phpteda\Generator\Configuration;

/**
 * Class that holds property to configure generator
 *
 * @author Jens Wiese <jens@howtrueisfalse.de>
 * @since 2013-03-19
 */
class ConfiguratorProperty
{
    const TYPE_BOOL = 1;
    const TYPE_MIXED = 2;

    /** @var string */
    protected $name;

    /** @var string */
    protected $question;

    /** @var mixed */
    protected $value;

    /** @var int */
    protected $type;

    /**
     * @param array $data
     */
    public function fromArray(array $data)
    {
        $this->name = $data['name'];
        $this->value = $data['value'];
        $this->question = $data['question'];
        $this->type = $data['type'];
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $question
     */
    public function setQuestion($question)
    {
        $this->question = rtrim($question, '?') . '?';
    }

    /**
     * @return string
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * @param int $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return bool
     */
    public function isBool()
    {
        return ($this->type == self::TYPE_BOOL);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $data = array();
        $data['name'] = $this->name;
        $data['value'] = $this->value;
        $data['question'] = $this->question;
        $data['type'] = $this->type;

        return $data;
    }
}