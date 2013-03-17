<?php

namespace Phpteda\Util;

use DirectoryIterator;
use FilterIterator;

/**
 * Class for ...
 *
 * @author jens
 * @since 2013-03-16
 */
class GeneratorDirectoryIterator extends FilterIterator
{
    /**
     * @param DirectoryIterator $iterator
     */
    public function __construct(DirectoryIterator $iterator)
    {
        parent::__construct($iterator);
    }

    /**
     * Checks whether current element is a Generator
     *
     * @return bool
     */
    public function accept()
    {
        /** @var DirectoryIterator $entry  */
        $entry = $this->getInnerIterator()->current();

        if (!$entry->isFile()) {
            return false;
        }

        if (false === strpos($entry->getFilename(), 'Generator.php')) {
            return false;
        }

        return true;
    }
}