<?php

namespace Phpteda\Test\Reflection;

use Phpteda\Reflection\ClassAnnotationReader;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2013-03-12 at 16:27:44.
 */
class ClassAnnotationReaderTest extends \PHPUnit_Framework_TestCase
{
    public function testRetrievingAllClassAnnotations()
    {
        $classReader = new ClassAnnotationReader(
            new \ReflectionClass('Phpteda\Test\Reflection\TestClassForReflection')
        );

        $actualAnnotations = $classReader->getAnnotations();
        $expectedAnnotations = array(
            'author' => array('Jens Wiese'),
            'since' => array('1.0.0'),
            'method' => array(
                'string getName() Returns a name',
                'string parseName(string $name) Parses name'
            )
        );

        $this->assertEquals($expectedAnnotations, $actualAnnotations);
    }

    public function testRetrievingSpecificClassAnnotations()
    {
        $classReader = new ClassAnnotationReader(
            new \ReflectionClass('Phpteda\Test\Reflection\TestClassForReflection')
        );

        $actualAnnotation = $classReader->getAnnotations('author');
        $expectedAnnotation = array('Jens Wiese');
        $this->assertEquals($expectedAnnotation, $actualAnnotation);

        $actualAnnotation = $classReader->getAnnotations('method');
        $expectedAnnotation = array(
            'string getName() Returns a name',
            'string parseName(string $name) Parses name'
        );
        $this->assertEquals($expectedAnnotation, $actualAnnotation);
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
        $classReader = new ClassAnnotationReader(
            new \ReflectionClass('Phpteda\Test\Reflection\TestClassForReflection')
        );
        $methodParts = $classReader->parseMagicMethodAnnotation($methodString);
        $actualString = implode(',', $methodParts);

        $this->assertEquals($expectedString, $actualString);
    }
}