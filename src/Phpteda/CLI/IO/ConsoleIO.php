<?php

namespace Phpteda\CLI\IO;

use Symfony\Component\Console\Helper\DialogHelper;
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
     * @param string $message
     * @param bool $newline
     */
    public function write($message, $newline = true)
    {
        $this->output->write($message, $newline);
    }

    /**
     * @param string $message
     * @param bool $newline
     */
    public function writeInfo($message, $newline = true)
    {
        $message = sprintf('<info>%s</info>', $message);
        $this->write($message, $newline);
    }

    /**
     * @param string $message
     * @param bool $newline
     */
    public function writeComment($message, $newline = true)
    {
        $message = sprintf('<comment>%s</comment>', $message);
        $this->write($message, $newline);
    }

    /**
     * @param string $message
     * @param bool $newline
     */
    public function writeError($message, $newline = true)
    {
        $message = sprintf('<error>%s</error>', $message);
        $this->write($message, $newline);
    }

    /**
     * @param $message
     */
    public function writeHeader($message)
    {
        $message = $message . ':';

        $this->write('');
        $this->writeComment($message);
        $this->writeComment(str_pad('', strlen($message), '-'));
    }

    /**
     * @param string $question
     * @param mixed $default
     * @return string The answer
     */
    public function ask($question, $default = null)
    {
        $choiceInfo = is_null($default) ? '' : sprintf(' [%s]', $default);
        $question = sprintf('<question>%s</question>%s: ', $question, $choiceInfo);

        return $this->helperSet->get('dialog')->ask($this->output, $question, $default);
    }

    /**
     * @param string $question
     * @param mixed $default
     * @return string The answer
     */
    public function askConfirmation($question, $default = true)
    {
        $choiceInfo = sprintf(
            '[%s/%s]',
            ($default === true ? 'Y' : 'y'),
            ($default === false ? 'N' : 'n')
        );
        $question = sprintf('<question>%s</question> %s: ', $question, $choiceInfo);

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
        $choiceInfo = is_null($default) ? '' : sprintf(' [%s]', $default);
        $question = sprintf('<question>%s</question>%s: ', $question, $choiceInfo);

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
        $choiceInfo = is_null($default) ? '' : sprintf(' [%s]', $default);
        $question = sprintf('<question>%s</question>%s: ', $question, $choiceInfo);

        return $this->helperSet->get('dialog')->ask($this->output, $question, $default, $suggestions);
    }

    /**
     * Provides choice for user input
     *
     * @param string $question
     * @param array $options
     * @param bool $allowEmptyChoice
     * @param mixed $default
     * @return string
     * @throws \Exception
     */
    public function choice($question, array $options, $allowEmptyChoice = true, $default = null)
    {
        $this->writeHeader($question);

        foreach ($options as $key => $option) {
            $this->write(sprintf('[%s] %s', $key, $option));
        }

        $validValues = array_keys($options);
        $validator = function ($choosenValue) use ($validValues, $allowEmptyChoice, $default) {
            $isValidValue = in_array($choosenValue, $validValues);
            $isAllowedEmpty = empty($choosenValue) && $allowEmptyChoice;

            if ($isValidValue) {
                return  $choosenValue;
            } elseif ($isAllowedEmpty) {
                return $default;
            }

            throw new \Exception(sprintf('Value "%s" is invalid', $choosenValue));
        };

        $message = 'Choose' . ($allowEmptyChoice ? ' (ENTER for no choice)' : '') . ': ';

        return $this->askAndValidate($message, $validator, false, null);
    }
}