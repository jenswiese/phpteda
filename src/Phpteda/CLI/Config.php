<?php

namespace Phpteda\CLI;

/**
 * Class for CLI configuration
 *
 * @author Jens Wiese <jens@howtrueisfalse.de>
 * @since 2013-03-15
 *
 * @method setGeneratorDirectory($path)
 * @method string getGeneratorDirectory()
 */
class Config
{
    const CONFIGFILE_NAME = '.phpteda';

    /** @var string */
    protected $directory;

    /** @var string */
    protected $filepath;

    /** @var array */
    protected $configuration;

    /**
     * @param $directory
     */
    public function __construct($directory)
    {
        $this->directory = $directory;
        $this->filepath = $directory . DIRECTORY_SEPARATOR . self::CONFIGFILE_NAME;
        $this->configuration = $this->readConfigurationFromFile();
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed|Config
     * @throws \RuntimeException
     */
    public function __call($name, $arguments)
    {
        if ('get' == substr($name, 0, 3)) {
            $configParam = substr($name, 3);
            $returnValue = isset($this->configuration[$configParam]) ? $this->configuration[$configParam] : null;
        } elseif ('set' == substr($name, 0, 3)) {
            $configParam = substr($name, 3);
            $this->configuration[$configParam] = (count($arguments) == 1) ? $arguments[0] : $arguments;
            $returnValue = $this;
        } else {
            throw new \RuntimeException("Method '" . $name . "' does not exists.");
        }

        return $returnValue;
    }

    /**
     * Save configuration to file
     */
    public function save()
    {
        file_put_contents($this->filepath, serialize($this->configuration));
    }

    /**
     * @return array
     */
    protected function readConfigurationFromFile()
    {
        if (!file_exists($this->filepath)) {
            return array();
        }

        $serializedConfig = file_get_contents($this->filepath);
        return (array) unserialize($serializedConfig);
    }
}