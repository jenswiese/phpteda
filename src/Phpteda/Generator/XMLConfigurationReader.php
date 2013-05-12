<?php

namespace Phpteda\Generator;

use XMLReader;

/**
 * Class XMLConfigurationReader
 *
 * @author jens
 * @since 2013-05-10
 */
class XMLConfigurationReader
{
    /** @var XMLReader */
    protected $xmlReader;

    public function __construct()
    {
        $this->reader = new XMLReader();
    }

    /**
     * @param \XMLReader $xmlReader
     */
    public function setXmlReader(XMLReader $xmlReader)
    {
        $this->xmlReader = $xmlReader;
    }

    /**
     * @param string $xml
     */
    public function fromString($xml)
    {
        $this->reader->XML($xml);
    }

    /**
     * @return bool
     */
    public function read()
    {
        while ($this->reader->read()) {
            if ($this->reader->nodeType == XMLReader::ELEMENT) {
                break;
            }
        }
    }

    /**
     * @return string
     */
    public function getElementName()
    {
        return $this->reader->localName;
    }

    /**
     * @return bool
     */
    public function next()
    {
        return $this->reader->next();
    }

    /**
     * @return bool
     */
    public function isGroup()
    {
        return ('group' == $this->reader->localName);
    }

    /**
     * @return bool
     */
    public function isProperty()
    {
        return ('property' == $this->getElementName());
    }

    /**
     * @return bool
     */
    public function isPropertyWithOptions()
    {
        return ('property' == $this->reader->localName && $this->hasChildNodes());
    }

    /**
     * @return bool
     */
    public function isBooleanProperty()
    {
        return ('boolean' == $this->reader->localName);
    }

    /**
     * @return bool
     */
    public function isTextNode()
    {
        return ('#text' == $this->getElementName());
    }

    /**
     * @param string $name
     * @return string
     */
    public function getAttribute($name)
    {
        return $this->reader->getAttribute($name);
    }

    /**
     * @return string
     */
    public function getElementValue()
    {
        return $this->reader->readString();
    }

    /**
     * @return bool
     */
    public function hasChildNodes()
    {
        return $this->reader->expand()->hasChildNodes();
    }

    /**
     * @return array
     */
    public function getPropertyOptions()
    {
        $reader = new XMLReader();
        $reader->XML($this->reader->readOuterXml());

        $options = array();
        while ($reader->read()) {
            if ('option' == $reader->localName && ($reader->nodeType == XMLReader::ELEMENT)) {
                $name = $reader->readString();
                $value = $reader->getAttribute('value');
                $options[$name] = $value;
            }
        }

        return $options;
    }
}
