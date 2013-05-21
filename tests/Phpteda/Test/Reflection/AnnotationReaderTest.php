<?php

namespace Phpteda\Test\Reflection;

use Phpteda\Reflection\AnnotationReader;

/**
 * Class for ...
 *
 * @author Jens Wiese <jens@howtrueisfalse.de>
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
 */
EOT;

        $this->reader = new AnnotationReader($docComment);
    }

    public function testGettingDescription()
    {
        $expectedDescription = 'This is a test class for class reflection, nothing more, nothing less';
        $actualDescription = $this->reader->getDescription();

        $this->assertEquals($expectedDescription, $actualDescription);
    }
}
