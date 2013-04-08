<?php

namespace Phpteda\CLI\IO;

use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class to access ConsoleIO
 *
 * (heavily inspired by Composer\IO, https://github.com/composer/composer)
 *
 * @author Jens Wiese <jens@howtrueisfalse.de>
 * @since 2013-04-07
 */
class ConsoleIO
{
    /** @var \Symfony\Component\Console\Input\InputInterface */
    protected $input;

    /** @var \Symfony\Component\Console\Output\OutputInterface */
    protected $output;

    /** @var \Symfony\Component\Console\Helper\HelperSet */
    protected $helperSet;

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param HelperSet $helperSet
     */
    public function __construct(InputInterface $input, OutputInterface $output, HelperSet $helperSet)
    {
        $this->input = $input;
        $this->output = $output;
        $this->helperSet = $helperSet;
    }


    /**
     * @param string $name
     * @param mixed $value
     */
    public function setArgument($name, $value)
    {
        $this->input->setArgument($name, $value);
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getArgument($name)
    {
        return $this->input->getArgument($name);
    }

    /**
     * @param string $messages
     * @param bool $newline
     */
    public function write($messages, $newline = true)
    {
        $this->output->write($messages, $newline);
    }

    /**
     * @param string $question
     * @param mixed $default
     * @return string The answer
     */
    public function ask($question, $default = null)
    {
        return $this->helperSet->get('dialog')->ask($this->output, $question, $default);
    }

    /**
     * @param string $question
     * @param mixed $default
     * @return string The answer
     */
    public function askConfirmation($question, $default = true)
    {
        return $this->helperSet->get('dialog')->askConfirmation($this->output, $question, $default);
    }

    /**
     * @param string $question
     * @param $validator
     * @param bool $attempts
     * @param mixed $default
     * @return string The answer
     */
    public function askAndValidate($question, $validator, $attempts = false, $default = null)
    {
        return $this->helperSet->get('dialog')->askAndValidate(
            $this->output,
            $question,
            $validator,
            $attempts,
            $default
        );
    }

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