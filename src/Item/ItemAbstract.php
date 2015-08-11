<?php

namespace Certi\LegacypsrFour\Item;

abstract class ItemAbstract
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

    /**
     * Gets property by name
     *
     * @param string $property
     *
     * @return mixed
     */
    public function get($property)
    {
        if (!isset($this->$property)) {
            throw new \InvalidArgumentException('Object of ' . __CLASS__ . ' has no property:' . $property);
        }
        return $this->$property;
    }

    public function __toString()
    {
        if ($this->name) {

            return $this->name . ' (' . $this->index . ')';
        }
        return 'undefined';
    }

}
