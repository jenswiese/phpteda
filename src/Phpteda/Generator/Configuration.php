<?php

namespace Phpteda\Generator;

/**
 * Class for ...
 *
 * @author jens
 * @since 2013-03-08
 */
class Configuration
{
    /** @var array */
    private $options = array();

    /**
     * @param $name
     */
    public function setBooleanOption($name)
    {
        $this->setOption($name, true);
    }

    /**
     * @param $name
     * @param bool $value
     */
    public function setOption($name, $value)
    {
        $this->options[$name] = $value;
    }

    /**
     * @param $name
     * @return bool
     */
    public function __get($name)
    {
        if (isset($this->options[$name])) {
            return $this->options[$name];
        }

        return false;
    }
}
