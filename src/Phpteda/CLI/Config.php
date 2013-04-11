<?php

namespace Phpteda\CLI;

/**
 * Class for CLI configuration
 *
 * @author Jens Wiese <jens@howtrueisfalse.de>
 * @since 2013-03-15
 *
 * @method setGeneratorDirectory(string $path)
 * @method bool hasGeneratorDirectory()
 * @method string getGeneratorDirectory()
 *
 * @method setBootstrapPathname(string $path)
 * @method bool hasBootstrapPathname()
 * @method string getBootstrapPathname()
 *
 * @method setGeneratorNamespace(string $namespace)
 * @method bool hasGeneratorNamespace()
 * @method string getGeneratorNamespace()
 *
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
     * @return mixed|Config|bool
     * @throws \RuntimeException
     */
    public function __call($name, $arguments)
    {
        if ('get' == substr($name, 0, 3)) {
            $returnValue = $this->handleGetterMethod($name);
        } elseif ('set' == substr($name, 0, 3)) {
            $returnValue = $this->handleSetterMethod($name, $arguments);
        } elseif ('has' == substr($name, 0, 3)) {
            $returnValue = $this->handleHasMethod($name);
        } else {
            throw new \RuntimeException("Method '" . $name . "' does not exists.");
        }

        return $returnValue;
    }

    /**
     * @param $name
     * @return bool
     */
    protected function handleHasMethod($name)
    {
        $configParam = substr($name, 3);
        $returnValue = isset($this->configuration[$configParam]);

        return $returnValue;
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return Config
     */
    protected function handleSetterMethod($name, $arguments)
    {
        $configParam = substr($name, 3);
        $this->configuration[$configParam] = (count($arguments) == 1) ? $arguments[0] : $arguments;
        $this->save();

        return $this;
    }

    /**
     * @param string $name
     * @return null|mixed
     */
    protected function handleGetterMethod($name)
    {
        $configParam = substr($name, 3);
        $returnValue = isset($this->configuration[$configParam]) ? $this->configuration[$configParam] : null;

        return $returnValue;
    }

    /**
     * Save configuration to file
     */
    protected function save()
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