<?php

namespace Phpteda\Test\Generator;

use Mockery;
use Mockery\Mock;
use Phpteda\Test\Generator\TestGenerator;
use Phpteda\Test\Generator\TestFakerProvider;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2013-03-10 at 11:30:02.
 */
class AbstractGeneratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AbstractGenerator | PHPUnit_Framework_MockObject_MockObject
     */
    protected $object;

    /** @var \Faker\Generator | \Mockery\MockInterface */
    protected $fakerMock;

    /** @var \Mockery\MockInterface */
    protected $verificationMock;

    protected function setUp()
    {
        $this->fakerMock = Mockery::mock('\Faker\Generator')->shouldIgnoreMissing();
        $this->object = TestGenerator::generate($this->fakerMock);

        $this->verificationMock = Mockery::mock('CallVerificationDummy');
        $this->verificationMock->shouldReceive('removeExistingData')->andReturnNull()->byDefault();
        $this->verificationMock->shouldReceive('generateData')->andReturnNull()->byDefault();

        $this->object->setVerificationMock($this->verificationMock);
    }

    protected function tearDown()
    {
        Mockery::close();
    }

    public function testDefiningBooleanOptionViaMagicMethod()
    {
        $this->object->testMethodShouldWork();
        $this->assertTrue($this->object->testMethodShouldWork);
    }

    public function testDefiningOptionWithValueViaMagicMethod()
    {
        $this->object->testMethodShouldWork('testValue');
        $this->assertEquals('testValue', $this->object->testMethodShouldWork);
    }

    public function testCallingNotExistingProperty()
    {
        $this->assertFalse($this->object->testMethodShouldWork);
    }

    public function testShouldRemoveExistingData()
    {
        $this->verificationMock->shouldReceive('removeExistingData')->once();
        $this->object->shouldRemoveExistingData();
        $this->object->amount(1);
    }

    public function testAmount()
    {
        $this->verificationMock->shouldReceive('generateData')->times(100);
        $this->object->amount(100);
    }

    public function testImplicitFakerInstantiationWithAnnotations()
    {
        $faker = TestGenerator::generate()->getTestDataGenerator();

        foreach ($faker->getProviders() as $provider) {
            if ($provider instanceof TestFakerProvider) {
                return;
            }
        }

        $this->fail("Should contain provider 'TestFakerProvider'");
    }
}
