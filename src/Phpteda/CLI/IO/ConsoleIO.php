<?php

namespace Phpteda\CLI\IO;

use Composer\IO\ConsoleIO as ComposerIO;

/**
 * Class ConsoleIO
 *
 * @author jens
 * @since 2013-04-07
 */
class ConsoleIO extends ComposerIO
{
    /**
     * @param string $question
     * @param array $suggestions
     * @param mixed $default
     * @return string The answer
     */
    public function askWithSuggestions($question, array $suggestions, $default = null)
    {
        return $this->helperSet->get('dialog')->ask($this->output, $question, $default, $suggestions);
    }


    /**
     * @param string $question
     * @param array $options
     * @param mixed $default
     * @return string The answer
     */
    public function select($question, array $options, $default = null)
    {
        $attempts = false;
        $errorMessage = 'Value "%s" is invalid';

        return $this->helperSet->get('dialog')->select($this->output, $question, $options, $default, $attempts, $errorMessage);
    }
}