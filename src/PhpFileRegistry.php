<?php

namespace Certi\LegacypsrFour;


use Certi\LegacypsrFour\Item\Instantation;

class PhpFileRegistry
{

    const PROP_OBJECT = 'object';

    /**
     * @var PhpFile[]
     */
    private $fileRegistry      = [];

    /**
     * @var string[]
     */
    private $classRegistry     = [];
    // private $interfaceRegistry = [];

    public function addFile(PhpFile $file)
    {
        $this->addIntoFileRegistry($file);
        $this->index($file);
    }

    /**
     * Get the indexed file
     *
     * @param string $id File hash
     *
     * @return PhpFile
     *
     * @throws InvalidArgumentException
     */
    public function getPhpFileById($id)
    {
        if (!isset($this->fileRegistry[$id])) {
            throw new \InvalidArgumentException(sprintf('File with ID: %s not found', $id));
        }
        return $this->fileRegistry[$id][self::PROP_OBJECT];
    }

    /**
     * Gets list of file ids
     *
     * @return array
     */
    public function getFileIdList()
    {
        return array_keys($this->fileRegistry);
    }

    /**
     * How many files in registry
     *
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
        $this->fileRegistry[$file->getID()][self::PROP_OBJECT] = $file;
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
        $str = [];

        $str[] = '--------------------';
        $str[] = 'Files in Registry: ' . $this->countFileRegistry();
        $str[] = 'Classes in Registry: ' . $this->countClassRegistry();
        foreach ($this->classRegistry as $class => $id) {

            $str[] = str_pad($id, 12, ' ') . $class .  ', path:' . $this->getPhpFileById($id)->getTargetNamespace();
        }

        /*
        $str .= 'interfaces in Registry: ' . $this->countInterfaceRegistry() . PHP_EOL;
        foreach ($this->interfaceRegistry as $class => $id) {
            $str .= str_pad($id, 12, ' ') . $class . PHP_EOL;
        }
        */

        return implode(PHP_EOL, $str);
    }

    /**
     * @return bool
     */
    public function isGlobalScopeInstantation(Instantation $instantation)
    {
        $sep = '\\';

        $res  = $instantation->getName() . ' => ';
        $res .= strpos($instantation->getName(), '\\', 0) . PHP_EOL;
#        echo $res;

        if (strpos($instantation->getName(), $sep) === 0) {
            return true;
        }

        return false;

        /* error!
        $sep = '\\';
        if (preg_match('#^' . $sep . '#', $instantation->getName())) {
            return true;
        }*/
    }

    /**
     * @todo implement me!
     *
     * @return bool
     */
    public function getUseNamespaceByInstantation()
    {
        return false;
    }
}
