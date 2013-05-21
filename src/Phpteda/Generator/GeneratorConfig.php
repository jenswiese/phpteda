<?php

namespace Phpteda\Generator;

use Phpteda\Generator\Configuration\Property;
use Phpteda\Generator\Configuration\PropertyGroup;
use Phpteda\Generator\XMLConfigurationReader;

/**
 * Class GeneratorConfig
 *
 * @author jens
 * @since 2013-05-10
 */
class GeneratorConfig
{
    /** @var XMLConfigurationReader */
    protected $xmlReader;

    /** @var PropertyGroup[] */
    protected $propertyGroups = array();


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->setXmlReader(new XMLConfigurationReader());
    }

    /**
     * @param XMLConfigurationReader $xmlReader
     */
    public function setXmlReader(XMLConfigurationReader $xmlReader)
    {
        $this->xmlReader = $xmlReader;
    }

    /**
     * Read config from xml
     *
     * @param string $xmlString
     * @return $this
     */
    public function readFromXml($xmlString)
    {
        $this->xmlReader->fromString($xmlString);

        while ($this->xmlReader->read()) {
            if ($this->xmlReader->isGroup()) {
                $groupTitle = $this->xmlReader->getAttribute('title');
                $this->propertyGroups[$groupTitle] = new PropertyGroup($groupTitle);
            } elseif ($this->xmlReader->isProperty()) {
                $property = new Property();
                $property->setName($this->xmlReader->getAttribute('name'));
                $property->setQuestion($this->xmlReader->getElementValue());
                $property->setType(Property::TYPE_MIXED);
                $this->propertyGroups[$groupTitle]->addProperty($property);
            } elseif ($this->xmlReader->isBooleanProperty()) {
                $property = new Property();
                $property->setName($this->xmlReader->getAttribute('name'));
                $property->setQuestion($this->xmlReader->getElementValue());
                $property->setType(Property::TYPE_BOOL);
                $this->propertyGroups[$groupTitle]->addProperty($property);
            } elseif ($this->xmlReader->isPropertyWithOptions()) {
                $property = new Property();
                $property->setName($this->xmlReader->getAttribute('name'));
                $property->setQuestion($this->xmlReader->getAttribute('title'));
                foreach ($this->xmlReader->getPropertyOptions() as $value => $name) {
                    $property->addOption($name, $value);
                }
                $this->propertyGroups[$groupTitle]->addProperty($property);
                $this->xmlReader->next();
            }
        }

        return $this;
    }

    /**
     * @return PropertyGroup[]
     */
    public function getPropertyGroups()
    {
        return $this->propertyGroups;
    }
}
