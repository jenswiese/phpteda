<?php

namespace Phpteda\Test\Reflection;

use Phpteda\Reflection\AnnotationReader;

/**
 * Class for ...
 *
 * @author jens
 * @since 2013-03-25
 */
class AnnotationReaderTest extends \PHPUnit_Framework_TestCase
{
    /** @var AnnotationReader */
    protected $reader;

    protected function setUp()
    {
        $docComment = <<<'EOT'
/**
 * This is a test class for class reflection,
 * nothing more, nothing less
 *
 * @author Jens Wiese
 * @since 1.0.0
 *
 * @fakerLocale de_DE
 * @fakerProvider \path\to\customProviderOne
 * @fakerProvider \path\to\customProviderTwo
 *
 * @method string getName() Returns a name
 * @method string parseName(string $name) Parses name
 *
 * <group name="Group 1">
 * @method string method1Group1() Returns a name
 * @method string method2Group1() Returns a name
 * </group>
 *
 * <group name="Group 2">
 * @method mixed method1Group2() Returns something
 * @method mixed method2Group2() Returns something
 * </group>
 *
 * <select name="Selectable 1">
 * @method mixed method1Selectable1() Returns something
 * @method mixed method2Selectable1() Returns something
 * </select>
 *
 * <select name="Selectable 2">
 * @method string method1Selectable2() Returns something
 * @method string method2Selectable2() Returns something
 * </select>
 */
EOT;

        $this->reader = new AnnotationReader($docComment);
    }


    public function testReadSelectableMethodAnnotations()
    {
        $expectedAnnotations = array(
            'select' => array(
                'Selectable 1' => array(
                    'mixed method1Selectable1() Returns something',
                    'mixed method2Selectable1() Returns something'
                ),
                'Selectable 2' => array(
                    'string method1Selectable2() Returns something',
                    'string method2Selectable2() Returns something'
                )
            )
        );

        $actualAnnotations = $this->reader->getSelectableMethodAnnotations();

        $this->assertEquals($expectedAnnotations, $actualAnnotations);
    }


    public function testReadGroupedMethodAnnotations()
    {
        $expectedAnnotations = array(
            'select' => array(
                'Group 1' => array(
                    'mixed method1Selectable1() Returns something',
                    'mixed method2Selectable1() Returns something'
                ),
                'Group 2' => array(
                    'string method1Selectable2() Returns something',
                    'string method2Selectable2() Returns something'
                )
            )
        );

        $actualAnnotations = $this->reader->getSelectableMethodAnnotations();

        $this->assertEquals($expectedAnnotations, $actualAnnotations);

    }


    public function testReadUntaggedAnnotations()
    {

    }


    public function testReadAllAnnotations()
    {

    }


    public function testGettingDescription()
    {
        
    }

}
