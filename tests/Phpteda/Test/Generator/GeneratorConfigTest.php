<?php

namespace Phpteda\Test\Generator;

use Phpteda\Generator\Configuration\Property;
use Phpteda\Generator\GeneratorConfig;
use Phpteda\Generator\XMLConfigurationReader;

/**
 * @author Jens Wiese <jens@howtrueisfalse.de>
 * @since 2013-05-12
 */
class GeneratorConfigTest extends \PHPUnit_Framework_TestCase
{
    /** @var GeneratorConfig */
    protected $config;

    protected function setUp()
    {
        $this->config = new GeneratorConfig();
        $this->config->setXmlReader(new XMLConfigurationReader());
    }

    public function testGroupWithProperty()
    {
        $xml = '
            <config>
                <group title="Test Group">
                    <property name="testProperty1">Enter value</property>
                </group>
            </config>
        ';

        $propertyGroups = $this->config->readFromXml($xml)->getPropertyGroups();

        $this->assertCount(1, $propertyGroups, 'Expected count of groups is wrong.');
        $this->assertEquals('Test Group', $propertyGroups[0]->getName());

        /** @var Property[] $properties */
        $properties = $propertyGroups[0]->getProperties();

        $this->assertCount(1, $properties, 'Expected count of properties is wrong.');
        $this->assertEquals('testProperty1', $properties[0]->getName());
        $this->assertNull($properties[0]->getValue());
        $this->assertEquals('Enter value', $properties[0]->getQuestion());
        $this->assertEquals(Property::TYPE_MIXED, $properties[0]->getType());
        $this->assertEmpty($properties[0]->getOptions());
    }

    /**
     * @depends testGroupWithProperty
     */
    public function testGroupWithBooleanProperty()
    {
        $xml = '
            <config>
                <group title="Test Group">
                    <property name="testProperty1" type="boolean">Yes or no?</property>
                </group>
            </config>
        ';

        $propertyGroups = $this->config->readFromXml($xml)->getPropertyGroups();

        /** @var Property[] $properties */
        $properties = $propertyGroups[0]->getProperties();

        $this->assertCount(1, $properties, 'Expected count of properties is wrong.');
        $this->assertEquals('testProperty1', $properties[0]->getName());
        $this->assertNull($properties[0]->getValue());
        $this->assertEquals('Yes or no', $properties[0]->getQuestion());
        $this->assertTrue($properties[0]->isBool(), 'Should be type "boolean".');
        $this->assertEmpty($properties[0]->getOptions());
    }

    /**
     * @depends testGroupWithProperty
     */
    public function testGroupWithMultipleProperty()
    {
        $xml = '
            <config>
                <group title="Test Group 1">
                    <property name="testProperty1" type="multiple">Enter values</property>
                </group>
           </config>
        ';

        $propertyGroups = $this->config->readFromXml($xml)->getPropertyGroups();

        /** @var Property[] $properties */
        $properties = $propertyGroups[0]->getProperties();

        $this->assertCount(1, $properties, 'Expected count of properties is wrong.');
        $this->assertEquals('testProperty1', $properties[0]->getName());
        $this->assertNull($properties[0]->getValue());
        $this->assertEquals('Enter values', $properties[0]->getQuestion());
        $this->assertTrue($properties[0]->isMultiple(), 'Should be type "multiple".');
    }

    /**
     * @depends testGroupWithProperty
     */
    public function testGroupContainingPropertyWithOptions()
    {
        $xml = '
            <config>
                <group title="Test Group">
                    <property name="testProperty" title="Choose">
                        <option value="1">First</option>
                        <option value="2">Second</option>
                        <option value="3">Third</option>
                    </property>
                </group>
            </config>
        ';

        $propertyGroups = $this->config->readFromXml($xml)->getPropertyGroups();

        /** @var Property[] $properties */
        $properties = $propertyGroups[0]->getProperties();

        $this->assertCount(1, $properties, 'Expected count of properties is wrong.');
        $this->assertTrue($properties[0]->hasOptions());
        $this->assertNotEmpty($properties[0]->getOptions());

        $expectedOptions = array(
            '1' => 'First',
            '2' => 'Second',
            '3' => 'Third'
        );
        $this->assertEquals($expectedOptions, $properties[0]->getOptions());
    }

    /**
     * @depends testGroupWithProperty
     */
    public function testGroupContainingMoreThanOneProperty()
    {
        $xml = '
            <config>
                <group title="Test Group">
                    <property name="testProperty1">Enter value 1</property>
                    <property name="testProperty2">Enter value 2</property>
                </group>
            </config>
        ';

        $propertyGroups = $this->config->readFromXml($xml)->getPropertyGroups();

        /** @var Property[] $properties */
        $properties = $propertyGroups[0]->getProperties();

        $this->assertCount(2, $properties, 'Expected count of properties is wrong.');
        $this->assertEquals('testProperty1', $properties[0]->getName());
        $this->assertEquals('Enter value 1', $properties[0]->getQuestion());
        $this->assertEquals('testProperty2', $properties[1]->getName());
        $this->assertEquals('Enter value 2', $properties[1]->getQuestion());
    }

    /**
     * @depends testGroupWithProperty
     */
    public function testWithMultipleGroups()
    {
        $xml = '
            <config>
                <group title="Test Group 1">
                    <property name="testProperty1">Enter value 1</property>
                </group>
                <group title="Test Group 2">
                    <property name="testProperty2">Enter value 2</property>
                    <property name="testProperty3">Enter value 3</property>
                </group>
            </config>
        ';

        $propertyGroups = $this->config->readFromXml($xml)->getPropertyGroups();
        $this->assertCount(2, $propertyGroups, 'Expected to have 2 groups.');

        /** @var Property[] $properties1 */
        $properties1 = $propertyGroups[0]->getProperties();
        $this->assertCount(1, $properties1, 'Expected count of properties is wrong.');
        $this->assertEquals('testProperty1', $properties1[0]->getName());

        /** @var Property[] $properties1 */
        $properties2 = $propertyGroups[1]->getProperties();
        $this->assertCount(2, $properties2, 'Expected count of properties is wrong.');
        $this->assertEquals('testProperty2', $properties2[0]->getName());
        $this->assertEquals('testProperty3', $properties2[1]->getName());
    }
}
