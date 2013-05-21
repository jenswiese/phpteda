<?php

namespace Phpteda\Generator;

use XMLReader;

/**
 * Class XMLConfigurationReader
 *
 * @author Jens Wiese <jens@howtrueisfalse.de>
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
                return true;
            }
        }

        return false;
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
        return ('property' == $this->getElementName() && !$this->hasChildElements());
    }

    /**
     * @return bool
     */
    public function isPropertyWithOptions()
    {
        return ('property' == $this->getElementName() && $this->hasChildElements());
    }

    /**
     * @return bool
     */
    public function isBooleanProperty()
    {
        return (('property' == $this->getElementName()) && ('boolean' == $this->getAttribute('type')));
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
    public function hasChildElements()
    {
        $xml = new \SimpleXMLElement($this->reader->readOuterXml());

        return (0 < $xml->count());
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
