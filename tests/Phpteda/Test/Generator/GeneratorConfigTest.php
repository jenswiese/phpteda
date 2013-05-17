<?php

namespace Phpteda\Test\Generator;

use Phpteda\Generator\Configuration\Property;
use Phpteda\Generator\GeneratorConfig;
use Phpteda\Generator\XMLConfigurationReader;

/**
 * Class for ...
 *
 * @author jens
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
        $this->assertEquals('Test Group', $propertyGroups['Test Group']->getName());

        /** @var Property[] $properties */
        $properties = $propertyGroups['Test Group']->getProperties();

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
        $properties = $propertyGroups['Test Group']->getProperties();

        $this->assertCount(1, $properties, 'Expected count of properties is wrong.');
        $this->assertTrue($properties[0]->hasOptions());
        $this->assertNotEmpty($properties[0]->getOptions());

        $expectedOptions = array(
            'First' => '1',
            'Second' => '2',
            'Third' => '3'
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
        $properties = $propertyGroups['Test Group']->getProperties();

        $this->assertCount(2, $properties, 'Expected count of properties is wrong.');
        $this->assertEquals('testProperty1', $properties[0]->getName());
        $this->assertEquals('Enter value 1', $properties[0]->getQuestion());
        $this->assertEquals('testProperty2', $properties[1]->getName());
        $this->assertEquals('Enter value 2', $properties[1]->getQuestion());
    }
}
