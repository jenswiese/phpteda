<?php

namespace Phpteda\Test\Generator;

use Phpteda\Generator\XMLConfigurationReader;

/**
 * Class for ...
 *
 * @author jens
 * @since 2013-05-12
 */
class XMLConfigurationReaderTest extends \PHPUnit_Framework_TestCase
{
    /** @var XMLConfigurationReader */
    protected $reader;

    protected function setUp()
    {
        $this->reader = new XMLConfigurationReader();
        $this->reader->setXmlReader(new \XMLReader());
    }


    public function testReadingGroupWithProperty()
    {
        $xml = '
            <config>
                <group title="Test Group">
                    <property name="testProperty1">Enter value</property>
                </group>
            </config>
        ';

        $this->reader->fromString($xml);

        $this->reader->read();
        $this->assertEquals('config', $this->reader->getElementName(), 'Element config expected.');

        $this->reader->read();
        $this->assertTrue($this->reader->isGroup(), 'Element is expected to be a group.');
        $this->assertEquals('Test Group', $this->reader->getAttribute('title'), 'Title is wrong.');

        $this->reader->read();
        $this->assertTrue($this->reader->isProperty(), 'Element is expected to be a property.');
        $this->assertEquals('testProperty1', $this->reader->getAttribute('name'));
        $this->assertEquals('Enter value', $this->reader->getElementValue());

        $this->assertNull($this->reader->read());
    }

    /**
     * @depends testReadingGroupWithProperty
     */
    public function testReadingGroupWithBooleanProperty()
    {
        $xml = '
            <config>
                <group title="Test Group">
                    <boolean name="testProperty1">Enter yes or no</boolean>
                </group>
            </config>
        ';

        $this->reader->fromString($xml);

        $this->reader->read();
        $this->reader->read();
        $this->reader->read();
        $this->assertTrue($this->reader->isBooleanProperty(), 'Element is expected to be a boolean property.');
        $this->assertEquals('testProperty1', $this->reader->getAttribute('name'));
        $this->assertEquals('Enter yes or no', $this->reader->getElementValue());

        $this->assertNull($this->reader->read());
    }

    /**
     * @depends testReadingGroupWithProperty
     */
    public function testReadingGroupWithBooleanPropertyWithOptions()
    {
        $xml = '
            <config>
                <group title="Test Group">
                    <property title="Make a choice" name="testPropertyWithOption">
                        <option value="10">First option</option>
                        <option value="20">Second option</option>
                        <option value="30">Third option</option>
                    </property>
                </group>
            </config>
        ';

        $this->reader->fromString($xml);

        $this->reader->read();
        $this->reader->read();
        $this->reader->read();
        $this->assertTrue(
            $this->reader->isPropertyWithOptions(),
            'Element is expected to be a property with options.'
        );
        $this->assertEquals('testPropertyWithOption', $this->reader->getAttribute('name'));
        $this->assertEquals('Make a choice', $this->reader->getAttribute('title'));

        $expectedOptions = array(
            'First option' => '10',
            'Second option' => '20',
            'Third option' => '30'
        );

        $this->assertEquals($expectedOptions, $this->reader->getPropertyOptions());
    }

    /**
     * @depends testReadingGroupWithProperty
     */
    public function testReadingMultipleGroups()
    {
        $xml = '
            <config>
                <group title="Test Group 1">
                    <property name="testProperty1">Enter value</property>
                </group>
                <group title="Test Group 2">
                    <property name="testProperty2">Enter value</property>
                </group>
            </config>
        ';

        $this->reader->fromString($xml);

        $this->reader->read();
        $this->reader->read();
        $this->assertTrue($this->reader->isGroup(), 'Element is expected to be the 1st group.');
        $this->assertEquals('Test Group 1', $this->reader->getAttribute('title'), 'Title is wrong.');

        $this->reader->read();
        $this->reader->read();
        $this->assertTrue($this->reader->isGroup(), 'Element is expected to be the 2nd group.');
        $this->assertEquals('Test Group 2', $this->reader->getAttribute('title'), 'Title is wrong.');
    }

    /**
     * @depends testReadingGroupWithProperty
     */
    public function testReadingMultipleProperties()
    {
        $xml = '
            <config>
                <group title="Test Group">
                    <property name="testProperty1">Enter value</property>
                    <property name="testProperty2">Enter value</property>
                    <property name="testProperty3">Enter value</property>
                </group>
            </config>
        ';

        $this->reader->fromString($xml);

        $this->reader->read();
        $this->reader->read();
        $this->reader->read();
        $this->assertTrue($this->reader->isProperty(), 'Element is expected to be the 1st property.');
        $this->assertEquals('testProperty1', $this->reader->getAttribute('name'));
        $this->reader->read();
        $this->assertTrue($this->reader->isProperty(), 'Element is expected to be the 2nd property.');
        $this->assertEquals('testProperty2', $this->reader->getAttribute('name'));
        $this->reader->read();
        $this->assertTrue($this->reader->isProperty(), 'Element is expected to be the 3rd property.');
        $this->assertEquals('testProperty3', $this->reader->getAttribute('name'));
    }
}

