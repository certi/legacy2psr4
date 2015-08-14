<?php

namespace Certi\LegacypsrFour;


use Certi\LegacypsrFour\Item\Classes;
use Certi\LegacypsrFour\Item\Instantation;

class PhpFileRegistry
{

    const PROP_OBJECT = 'object';

    /**
     * @var PhpFile[]
     */
    private $fileRegistry      = [];

    /**
     * @var Classes
     */
    private $classRegistry     = [];
    // private $interfaceRegistry = [];

    public function addFile(PhpFile $file)
    {
        $this->addIntoFileRegistry($file);
        $this->addIntoClassRegistry($file);
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
     *
     * @todo: rechne die gleichnamige Klassen drauf
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


    protected function addIntoClassRegistry(PhpFile $file)
    {

        $classItem = $this->getClassByFile($file);
        if (empty($classItem)) {
            // this file containsts none class - probably old procedural code
            return;
        }

        if ($this->isClassDuplicated($classItem)) {

            // @todo create full msg
            #$msg  = sprintf('Class %s was already added', $file->getClassName()) . ', ' . $file->getRealPath();
            #$prev = $this->classRegistry[$file->getClassName()];

            $msg = 'abc';
            throw new \Exception($msg);
        }

        $this->insertIntoClassRegistry($classItem);

    }

    protected function getClassByFile(PhpFile $file)
    {
        $class = $file->getClassName();
        if (empty($class)) {
            return false;
        }
        $ns = $file->getCorrectNamespaceForClass();

        $classItem = new Item\Classes();
        $classItem->setName($class);
        $classItem->setNamespace($ns);
        $classItem->setFileID($file->getID());

        return $classItem;
    }


    /**
     * @param $classItem
     *
     * @todo: rename it! see this->addIntoClassRegistry
     */
    protected function insertIntoClassRegistry(Classes $classItem)
    {
        if (!isset($this->classRegistry[$classItem->getName()])) {
            $this->classRegistry[$classItem->getName()] = [];
        }

        $this->classRegistry[$classItem->getName()][] = $classItem;

    }

    /**
     * Its possible to have to classes with the same name
     * but in different namespaces. Both must be check
     *
     * @param Classes $class
     */
    protected function isClassDuplicated(Item\Classes $class)
    {

        if (isset($this->classRegistry[$class->getName()])) {

            /**
             * @var Item\Classes $foundClass
             */
            foreach ($this->classRegistry[$class->getName()] as $foundClass) {
                if ($foundClass->getNamespace() === $class->getNamespace()) {
                    return true;
                }
            }


        }
        return false;

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
        foreach ($this->classRegistry as $className => $classList) {
            try {

                foreach ($classList as $class) {

                    $fileID = $class->getFileID();

                    $file = $this->getPhpFileById($fileID);

                    $str[] = str_pad($class->getFileID(), 12, ' ');
                    $msg  = $class->getName() ;
                    $msg .= ', path:' . $file->getTargetNamespace();
                    $str[] = $msg;
                }
            } catch (Exception $e) {
                echo $e->getTraceAsString();
            }

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
     * Detects global namespaces (for Example \Exception)
     * @return bool
     */
    public function isGlobalScopeInstantation(Instantation $instantation)
    {
        if (strpos($instantation->getName(), PhpFile::NS_SEPARATOR) === 0) {
            return true;
        }
        return false;

        /* error!
        if (preg_match('#^' . PhpFile::SEPARATOR . '#', $instantation->getName())) {
            return true;
        }*/
    }

    /**
     * @todo implement me!
     *
     * @return bool
     */
    public function getUseNamespaceByInstantation(Instantation $instantation)
    {

        if (isset($this->classRegistry[$instantation->getName()])) {
            echo "\t" . $instantation->getName() . ': ' . PHP_EOL;
            foreach ($this->classRegistry[$instantation->getName()] as $class) {
                echo "\t\t" . $class  . PHP_EOL;
            }
        } else {
            echo "\t" . $instantation->getName() . ' => not found' . PHP_EOL;
            // not found: use in global context
            if (strpos($instantation->getName(), PhpFile::NS_SEPARATOR) !== 0) {
                return PhpFile::NS_SEPARATOR . $instantation->getName();
            }
        }

        return false;
    }
}
