<?php

namespace Certi\LegacypsrFour\Item;

class Namespaces
{

    protected $name;

    protected $index;

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * @param mixed $line
     */
    public function setIndex($line)
    {
        $this->index = $line;
    }

    public function __toString()
    {
        if ($this->name) {

            return $this->name . ' (' . $this->index . ')';
        }
        return 'undefined';
    }

}
