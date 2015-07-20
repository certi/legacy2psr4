<?php

namespace Certi\LegacypsrFour;


class PhpFileRegistry
{

    private $fileRegistry      = [];
    private $classRegistry     = [];
    // private $interfaceRegistry = [];

    public function addFile(PhpFile $file)
    {
        $this->addIntoFileRegistry($file);
        $this->index($file);
    }

    /**
     * How many files in registry
     * @return int
     */
    public function countFileRegistry()
    {
        return count($this->fileRegistry);
    }

    /**
     * How many files in registry
     * @return int
     */
    public function countClassRegistry()
    {

        return count($this->classRegistry);
    }

    protected function addIntoFileRegistry(PhpFile $file)
    {
        if (isset($this->fileRegistry[$file->getID()])) {
            throw new \Exception(sprintf('File %s was already added', $file->getRealPath()));
        }
        $this->fileRegistry[$file->getID()]['object'] = $file;
    }


    protected function index(PhpFile $file)
    {
        $class = $file->getClassName();
        if (!empty($class)) {
            if (isset($this->classRegistry[$file->getClassName()])) {
                throw new \Exception(sprintf('Class %s was already added', $file->getClassName()) . ', ' . $file->getRealPath());
            }
            $this->classRegistry[$file->getClassName()] = $file->getID();
        }
        /*
        $interface = $file->getInterfaceName();
        if (!empty($interface)) {
            if (isset($this->interfaceRegistry[$file->getInterfaceName()])) {
                throw new \Exception(sprintf('Class %s was already added', $file->getInterfaceName()) . ', ' . $file->getRealPath());
            }
            $this->interfaceRegistry[$file->getInterfaceName()] = $file->getID();
        }*/

    }

    /**
     * Temporary added to see the statistics
     *
     * @return string
     */
    public function __toString()
    {
        $str = '--------------------' .PHP_EOL
             . 'Files in Registry: ' . $this->countFileRegistry() . PHP_EOL
        ;

        $str .= 'Classes in Registry: ' . $this->countClassRegistry() . PHP_EOL;
        foreach ($this->classRegistry as $class => $id) {
            $str .= str_pad($id, 12, ' ') . $class . PHP_EOL;
        }

        /*
        $str .= 'interfaces in Registry: ' . $this->countInterfaceRegistry() . PHP_EOL;
        foreach ($this->interfaceRegistry as $class => $id) {
            $str .= str_pad($id, 12, ' ') . $class . PHP_EOL;
        }
        */

        return $str;
    }

}
