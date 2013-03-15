<?php

namespace Phpteda\Test\Generator;

use Mockery\MockInterface;
use Phpteda\Generator\AbstractGenerator;

/**
 * Class for testing the AbstractGenerator, in order to check the
 * calls to the abstract methods that provides the major functionality
 *
 * @author Jens Wiese <jens@howtrueisfalse.de>
 * @since 2013-03-10
 *
 * @method TestGenerator isActive()
 * @method TestGenerator withValue($value)
 */
class TestGenerator extends AbstractGenerator
{
    protected $verificationMock;

    /**
     * @param MockInterface $verificationMock
     */
    public function setVerificationMock(MockInterface $verificationMock)
    {
        $this->verificationMock = $verificationMock;
    }

    /**
     * Implements custom way to delete existing data
     *
     * @return void
     */
    protected function removeExistingData()
    {
        $this->verificationMock->{__FUNCTION__}();
    }

    /**
     * Implements custom generator behaviour
     *
     * @return void
     */
    protected function generateData()
    {
        $this->verificationMock->{__FUNCTION__}();
    }
}
