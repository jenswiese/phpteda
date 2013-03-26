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
 * <select name="Selectable 1 ">
 * @method mixed method1Selectable1() Returns something
 * @method mixed method2Selectable1() Returns something
 * </select>
 *
 * <select name="Selectable 2 ">
 * @method string method1Selectable2() Returns something
 * @method string method2Selectable2() Returns something
 * </select>
 *
 * @method string lastMethod() Returns a name
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
            'group' => array(
                'Group 1' => array(
                    'string method1Group1() Returns a name',
                    'string method2Group1() Returns a name'
                ),
                'Group 2' => array(
                    'mixed method1Group2() Returns something',
                    'mixed method2Group2() Returns something'
                )
            )
        );

        $actualAnnotations = $this->reader->getGroupedMethodAnnotations();

        $this->assertEquals($expectedAnnotations, $actualAnnotations);

    }

    public function testUntaggedMethodAnnotations()
    {
        $expectedAnnotations = array(
            'string getName() Returns a name',
            'string parseName(string $name) Parses name',
            'string lastMethod() Returns a name'
        );

        $actualAnnotations = $this->reader->getUntaggedMethodAnnotations();
        $this->assertEquals($expectedAnnotations, $actualAnnotations);
    }


    public function testReadAllAnnotationsWithoutGrouping()
    {
        $expectedAnnotations = array(
            'author' => 'Jens Wiese',
            'since' => '1.0.0',
            'fakerLocale' => 'de_DE',
            'fakerProvider' => array(
                '\path\to\customProviderOne',
                '\path\to\customProviderTwo'
            ),
            'method' => array(
                'string getName() Returns a name',
                'string parseName(string $name) Parses name',
                'string method1Group1() Returns a name',
                'string method2Group1() Returns a name',
                'mixed method1Group2() Returns something',
                'mixed method2Group2() Returns something',
                'mixed method1Selectable1() Returns something',
                'mixed method2Selectable1() Returns something',
                'string method1Selectable2() Returns something',
                'string method2Selectable2() Returns something',
                'string lastMethod() Returns a name'
            )
        );

        $actualAnnotations = $this->reader->getAnnotations();

        $this->assertEquals($expectedAnnotations, $actualAnnotations);
    }

    public function testReadSpecificAnnotationWithoutGrouping()
    {
        $expectedAnnotations = 'Jens Wiese';

        $actualAnnotations = $this->reader->getAnnotations('author');

        $this->assertEquals($expectedAnnotations, $actualAnnotations);
    }

    public function testReadSpecificMultipleAnnotationsWithoutGrouping()
    {
        $expectedAnnotations = array(
            'string getName() Returns a name',
            'string parseName(string $name) Parses name',
            'string method1Group1() Returns a name',
            'string method2Group1() Returns a name',
            'mixed method1Group2() Returns something',
            'mixed method2Group2() Returns something',
            'mixed method1Selectable1() Returns something',
            'mixed method2Selectable1() Returns something',
            'string method1Selectable2() Returns something',
            'string method2Selectable2() Returns something',
            'string lastMethod() Returns a name'
        );

        $actualAnnotations = $this->reader->getAnnotations('method');

        $this->assertEquals($expectedAnnotations, $actualAnnotations);
    }

    public function testGettingDescription()
    {
        $expectedDescription = 'This is a test class for class reflection, nothing more, nothing less';
        $actualDescription = $this->reader->getDescription();

        $this->assertEquals($expectedDescription, $actualDescription);
    }

    public static function provideMagicMethodStrings()
    {
        return array(
            'complete' => array(
                'bool isValid(Form $form) Checks form object',
                'bool,isValid,Form,form,Checks form object'
            ),
            'without return-type' => array(
                'isValid(Form $form) Checks form object',
                ',isValid,Form,form,Checks form object'
            ),
            'without parameter' => array(
                'string getName() Returns name',
                'string,getName,,,Returns name'
            ),
            'without description' => array(
                'string getName()',
                'string,getName,,,'
            ),
            'with many spaces' => array(
                ' bool  isValid(Form $form)  Checks form object ',
                'bool,isValid,Form,form,Checks form object'
            )
        );
    }

    /**
     * @dataProvider provideMagicMethodStrings
     */
    public function testParseMagicMethodAnnotation($methodString, $expectedString)
    {
        $methodParts = $this->reader->parseMagicMethodAnnotation($methodString);
        $actualString = implode(',', $methodParts);

        $this->assertEquals($expectedString, $actualString);
    }
}
