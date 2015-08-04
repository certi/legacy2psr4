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
        return 'Name:' . $this->name ? $this->name : 'undefined' . ', line:' . $this->line;
    }

}
