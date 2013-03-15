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
    protected $filepath;

    /** @var array */
    protected $configuration;

    /**
     * @param $filepath
     */
    public function __construct($filepath)
    {
        $this->filepath = $filepath;
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
        file_put_contents($this->filepath . '/' . self::CONFIGFILE_NAME, serialize($this->configuration));
    }

    /**
     * @return array
     */
    protected function readConfigurationFromFile()
    {
        $serializedConfig = file_get_contents($this->filepath);
        return (array) unserialize($serializedConfig);
    }
}