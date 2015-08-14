<?php

namespace Certi\LegacypsrFour\Item;

class Classes extends ItemAbstract
{
    /**
     * @var string
     */
    private $namespace;

    /**
     * @var string
     */
    private $fileID;

    /**
     * @return mixed
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @param mixed $namespace
     */
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
    }

    /**
     * @return mixed
     */
    public function getFileID()
    {
        return $this->fileID;
    }

    /**
     * @param mixed $fileID
     */
    public function setFileID($fileID)
    {
        return $this->getName() . ' (ns: ' . $this->getNamespace() . ')';
    }

}
