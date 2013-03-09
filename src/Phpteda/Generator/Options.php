<?php

namespace Phpteda\Generator;

/**
 * Class for ...
 *
 * @author jens
 * @since 2013-03-08
 */
class Options
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
    public function hasOption($name)
    {
        return isset($this->options[$name]);
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getOption($name)
    {
        if ($this->hasOption($name)) {
            return $this->options[$name];
        }
    }
}
