<?php

namespace Phpteda\Reflection\Method;

/**
 * Class for ...
 *
 * @author jens
 * @since 2013-03-11
 */
class Method
{
    /** @var string */
    protected $name;

    /** @var string */
    protected $returnType;

    /** @var string */
    protected $parameterName;

    /** @var string */
    protected $parameterType;

    /** @var string */
    protected $description;

    /**
     * @param array $methodInfos
     */
    public function __construct(array $methodInfos = array())
    {
        if (!empty($methodInfos)) {
            $this->setName($methodInfos['name']);
            $this->setReturnType($methodInfos['returnType']);
            $this->setParameterName($methodInfos['parameterName']);
            $this->setParameterType($methodInfos['parameterType']);
            $this->setDescription($methodInfos['description']);
        }
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
     * @param string $parameterName
     */
    public function setParameterName($parameterName)
    {
        $this->parameterName = $parameterName;
    }

    /**
     * @return bool
     */
    public function hasParameter()
    {
        return $this->parameterName !== '';
    }

    /**
     * @return string
     */
    public function getParameterName()
    {
        return $this->parameterName;
    }

    /**
     * @param string $parameterType
     */
    public function setParameterType($parameterType)
    {
        $this->parameterType = $parameterType;
    }

    /**
     * @return string
     */
    public function getParameterType()
    {
        return $this->parameterType;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return bool
     */
    public function hasDescription()
    {
        return !empty($this->description);
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $returnType
     */
    public function setReturnType($returnType)
    {
        $this->returnType = $returnType;
    }

    /**
     * @return string
     */
    public function getReturnType()
    {
        return $this->returnType;
    }
}
