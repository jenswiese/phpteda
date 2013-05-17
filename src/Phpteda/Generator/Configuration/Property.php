<?php

namespace Phpteda\Generator\Configuration;

/**
 * Class that holds property to configure generator
 *
 * @author Jens Wiese <jens@howtrueisfalse.de>
 * @since 2013-03-19
 */
class Property implements PropertyInterface
{
    const TYPE_BOOL = 1;
    const TYPE_MIXED = 2;
    const TYPE_WITH_OPTION = 3;

    /** @var string */
    protected $name;

    /** @var string */
    protected $question;

    /** @var mixed */
    protected $value;

    /** @var int */
    protected $type;

    /** @var array */
    protected $options = array();

    /**
     * @param array $data
     */
    public function fromArray(array $data)
    {
        $this->name = $data['name'];
        $this->value = $data['value'];
        $this->question = $data['question'];
        $this->type = $data['type'];
        $this->options = $data['options'];

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
        $this->question = rtrim($question, '?');
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
     * @param string $name
     * @param string $value
     */
    public function addOption($name, $value)
    {
        $this->options[$name] = $value;
    }

    /**
     * @return bool
     */
    public function hasOptions()
    {
        return !empty($this->options);
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
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
        $data['options'] = $this->options;

        return $data;
    }
}