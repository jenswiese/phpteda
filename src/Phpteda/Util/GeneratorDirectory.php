<?php

namespace Phpteda\Util;

use Phpteda\Util\GeneratorDirectoryIterator;
use DirectoryIterator;

/**
 * Class for accessing GeneratorDirectory
 *
 * @author Jens Wiese <jens@howtrueisfalse.de>
 * @since 2013-03-17
 */
class GeneratorDirectory
{
    /** @var string */
    protected $path;

    /**
     * @param string $path
     */
    public function __construct($path)
    {
        $this->iterator = new GeneratorDirectoryIterator(new DirectoryIterator($path));
    }

    /**
     * @return array
     */
    public function getGeneratorNames()
    {
        $names = array();
        foreach ($this->iterator as $file) {
            $names[] = $file->getBasename('Generator.php');
        }

        return $names;
    }
}