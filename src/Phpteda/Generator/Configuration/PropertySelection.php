<?php

namespace Phpteda\Generator\Configuration;

use Phpteda\Reflection\Method\Method;

/**
 * Class PropertyGroup
 *
 * @author jens
 * @since 2013-03-26
 */
class PropertySelection
{
    /** @var string */
    protected $name;

    /** @var array */
    protected $properties = array();

    /** @var string */
    protected $selectedIndex;

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
     * @return array
     */
    public function getOptions()
    {
        $options = array();
        foreach ($this->properties as $key => $property) {
            $options[$key+1] = $property->getQuestion();
        }

        return $options;
    }

    /**
     * @param string $name
     * @return Property
     */
    public function setSelectedOptionByKey($index)
    {
        $this->selectedIndex = $index-1;
        $this->properties[$this->selectedIndex]->setValue(true);
    }

    /**
     * @return Property
     */
    public function getSelectedProperty()
    {
        return $this->properties[$this->selectedIndex];
    }
}