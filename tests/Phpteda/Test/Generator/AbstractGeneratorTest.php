<?php

namespace Phpteda\Test\Generator;

use Mockery;
use Mockery\Mock;
use Phpteda\Test\Generator\TestGenerator;
use Phpteda\Test\Generator\DummyTestDataProvider;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2013-03-10 at 11:30:02.
 */
class AbstractGeneratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AbstractGenerator | PHPUnit_Framework_MockObject_MockObject
     */
    protected $object;

    /** @var \Phpteda\TestData\Generator | \Mockery\MockInterface */
    protected $testDataGenerator;

    /** @var \Mockery\MockInterface */
    protected $verificationMock;

    protected function setUp()
    {
        $this->testDataGenerator = Mockery::mock('\Phpteda\TestData\Generator')->shouldIgnoreMissing();
        $this->object = TestGenerator::generate($this->testDataGenerator);

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

    public function testImplicitTestDataProviderSetting()
    {
        $generator = TestGenerator::generate()->getTestDataGenerator();

        foreach ($generator->getProviders() as $provider) {
            if ($provider instanceof DummyTestDataProvider) {
                return;
            }
        }

        $this->fail("Should contain provider 'DummyTestDataProvider'");
    }
}
