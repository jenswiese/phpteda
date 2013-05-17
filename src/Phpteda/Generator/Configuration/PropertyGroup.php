<?php

namespace Phpteda\Generator\Configuration;

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
     * @param PropertyInterface $property
     */
    public function addProperty(PropertyInterface $property)
    {
        $this->properties[] = $property;
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