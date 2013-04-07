<?php

namespace Phpteda\Generator\Configuration;

use Phpteda\Reflection\Method\Method;

/**
 * Class PropertyGroup
 *
 * @author jens
 * @since 2013-03-26
 */
class PropertyGroup
{
    /** @var string */
    protected $name;

    /** @var array */
    protected $properties = array();

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @param Method[] $methods
     */
    public function fromMethodArray(array $methods)
    {
        foreach ($methods as $method) {
            if (!$method->hasDescription()) {
                continue;
            }

            $property = new Property();
            $property->fromMethodObject($method);

            $this->properties[] = $property;
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return Property[]
     */
    public function getProperties()
    {
        return $this->properties;
    }
}