<?php

namespace Phpteda\CLI\Helper;

use Phpteda\CLI\IO\ConsoleIO;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class for displaying a simple Table in console
 *
 * @author: Jens Wiese <jens@dev.lohering.de>
 * @since: 2012-11-20
 */
class Table
{
    const BORDER_HORIZONTAL = '-';
    const BORDER_CORNER     = '+';
    const BORDER_VERTICAL   = '|';

    /** @var array */
    protected $columns = array();

    /** @var array */
    protected $columnCount = 1;

    /** @var array */
    protected $columnWidth;

    /** @var int */
    protected $lastColumnCount = 1;

    /** @var int */
    protected $lastColumnWidth;

    /** @var integer */
    protected $width;

    /** @var ConsoleIO */
    protected $io;

    /**
     * Creates Table
     *
     * @param ConsoleIO $io
     * @param integer $width
     * @return Table
     */
    public static function create(ConsoleIO $io, $width)
    {
        return new self($io, $width);
    }

    /**
     * Add row (terminates last row)
     *
     * @return Table
     */
    public function addRow()
    {
        if (!empty($this->columns)) {
            return $this->flushOutput();
        }

        return $this;
    }

    /**
     * Add column to current row
     *
     * @param $content
     * @param null $width
     * @return $this
     */
    public function addColumn($content, $width = null)
    {
        $this->columns[] = $content;

        return $this;
    }

    /**
     * Terminates table construction and flushes the last row
     */
    public function end()
    {
        $this->flushOutput();
        $this->outputBorder();
    }

    /**
     * Protected constructor of the class (use create())
     *
     * @param ConsoleIO $io
     * @param $width
     */
    protected function __construct(ConsoleIO $io, $width)
    {
        $this->io = $io;
        $this->width = $width;
    }

    /**
     * Flushes the current row
     * (implicit by adding a new row, or explicit by using end())
     *
     * @return Table
     */
    protected function flushOutput()
    {
        $this->columnCount = count($this->columns);
        $this->columnWidth = floor($this->width / $this->columnCount) - ($this->columnCount==1 ? 2 : 1);

        $this->outputBorder();

        $maxContentLengthPerColumn = ($this->columnWidth - 4);
        $columnRows = array();
        $maxRowCount = 1;
        foreach ($this->columns as $columnNum => $columnValue) {
            $columnRows[$columnNum] = explode(
                '%%',
                wordwrap($columnValue, $maxContentLengthPerColumn, '%%', true)
            );
            $currentCount = count($columnRows[$columnNum]);
            $maxRowCount = ($maxRowCount < $currentCount) ? $currentCount : $maxRowCount;
        }

        for ($row = 0; $row < $maxRowCount; $row++) {
            for ($column = 0; $column < $this->columnCount; $column++) {
                if (isset($columnRows[$column][$row])) {
                    $this->columns[$column] = $columnRows[$column][$row];
                } else {
                    $this->columns[$column] = '';
                }
            }
            $this->outputContent();
        }

        $this->columns = array();
        $this->lastColumnCount = $this->columnCount;
        $this->lastColumnWidth = $this->columnWidth;

        return $this;
    }

    /**
     * Outputs the top or bottom border
     */
    protected function outputBorder()
    {
        $columnCount = ($this->lastColumnCount > 1) ? $this->lastColumnCount : $this->columnCount;
        $columnWidth = ($this->lastColumnCount > 1) ? $this->lastColumnWidth : $this->columnWidth;

        for ($i = 0; $i < $columnCount; $i++) {
            $this->io->write(str_pad('+', $columnWidth, '-', STR_PAD_RIGHT), false);
        }
        $this->io->write('+');
    }

    /**
     * Outputs the content of the row (meaning all column contents)
     */
    protected function outputContent()
    {
        for ($i = 0; $i < $this->columnCount; $i++) {
            $valueCountForTags = strlen($this->columns[$i]) - strlen(strip_tags($this->columns[$i]));
            $columnWidth = (integer) $this->columnWidth + $valueCountForTags;

            $this->io->write(str_pad('| ' . $this->columns[$i], $columnWidth, ' ', STR_PAD_RIGHT), false);
        }
        $this->io->write('|');
    }
}
