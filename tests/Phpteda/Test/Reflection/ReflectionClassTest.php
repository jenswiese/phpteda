<?php

namespace Phpteda\Test\Reflection;

use Phpteda\Reflection\ReflectionClass;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2013-03-12 at 16:27:44.
 */
class ReflectionClassTest extends \PHPUnit_Framework_TestCase
{
    public function testRetrievingAllClassAnnotations()
    {
    }

    public function testRetrievingSpecificClassAnnotations()
    {
    }

    public function testRetrievingClassDescription()
    {
        $classReader = new ReflectionClass('Phpteda\Test\Reflection\TestClassForReflection');

        $actualDescription = $classReader->getDescription();
        $expectedDescription = 'This is a test class for class reflection, nothing more, nothing less';

        $this->assertEquals($expectedDescription, $actualDescription);
    }
}