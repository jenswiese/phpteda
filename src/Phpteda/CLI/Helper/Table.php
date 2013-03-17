<?php

namespace Phpteda\CLI\Helper;

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

    /** @var \Symfony\Component\Console\Output\Output */
    protected $output;

    /**
     * Creates Table
     *
     * @param OutputInterface $output
     * @param $width
     * @return Table
     */
    public static function create(OutputInterface $output, $width)
    {
        return new self($output, $width);
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
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param $width
     */
    protected function __construct(OutputInterface $output, $width)
    {
        $this->output = $output;
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
        $this->outputContent();

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
            $this->output->write(str_pad('+', $columnWidth, '-', STR_PAD_RIGHT));
        }
        $this->output->writeln('+');
    }

    /**
     * Outputs the content of the row (meaning all column contents)
     */
    protected function outputContent()
    {
        for ($i = 0; $i < $this->columnCount; $i++) {
            $valueCountForTags = strlen($this->columns[$i]) - strlen(strip_tags($this->columns[$i]));
            $columnWidth = (integer) $this->columnWidth + $valueCountForTags;

            $this->output->write(str_pad('| ' . $this->columns[$i], $columnWidth, ' ', STR_PAD_RIGHT));
        }
        $this->output->writeln('|');
    }
}
