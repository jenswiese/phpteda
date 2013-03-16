<?php

namespace Phpteda\Test\CLI;

use Phpteda\CLI\Config;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /** @var Config */
    protected $object;

    /** @var vfsStreamDirectory */
    protected $filesystem;

    protected function setUp()
    {
        $this->filesystem = vfsStream::setup('testDir');
        $this->object = new Config(vfsStream::url('testDir'));
    }

    public function testSettingAndGettingProperty()
    {
        $this->object->setGeneratorDirectory('generator/test/dir');
        $this->assertEquals('generator/test/dir', $this->object->getGeneratorDirectory());
    }

    public function testHasProperty()
    {
        $this->assertFalse($this->object->hasGeneratorDirectory());
        $this->object->setGeneratorDirectory('generator/test/dir');
        $this->assertTrue($this->object->hasGeneratorDirectory());
    }

    public function testSavingConfig()
    {
        $this->object->setGeneratorDirectory('generator/test/dir');
        $this->object->save();

        $this->assertTrue($this->filesystem->hasChild('.phpteda'));
        $actualConfiguration = unserialize($this->filesystem->getChild('.phpteda')->getContent());
        $expectedConfiguration = array('GeneratorDirectory' => 'generator/test/dir');

        $this->assertEquals($expectedConfiguration, $actualConfiguration);
    }

    /**
     * @depends testSavingConfig
     */
    public function testReadingConfig()
    {
        $this->object->setGeneratorDirectory('generator/test/dir');
        $this->object->save();

        $config = new Config(vfsStream::url('testDir'));

        $this->assertEquals('generator/test/dir', $config->getGeneratorDirectory());
    }
}
