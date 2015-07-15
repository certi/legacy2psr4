<?php

namespace Certi\LegacypsrFour;

use Symfony\Component\Finder;


class PhpFile
{

    /**
     * @var Finder\SplFileInfo
     */
    protected $file;

    protected $currentFileName;

    protected $realPath;

    protected $basePath;

    protected $className;

    protected $currentNamespace;

    protected $usesNamespaces = [];

    protected $instantiations = [];

    public function __construct(Finder\SplFileInfo $file, $basePath)
    {
        $this->file            = $file;
        $this->realPath        = $file->getRealPath();
        $this->currentFileName = $file->getFilename();
        $this->basePath        = realpath($basePath);

    }

    public function handle()
    {

        $checkList   = [];
        $checkList[] = new Checker\GetClassName($this);
        $checkList[] = new Checker\GetNamespace($this);
        $checkList[] = new Checker\GetUsesNamespaces($this);
        $checkList[] = new Checker\GetInstantiations($this);
        // $checkList[] = new Checker\GeStaticCalls($this);

        // type hinting
        // interfaces
        // traits

        // pass namespace to path?
        // pass class to filename?
        // has multiple namespaces?

        /**
         * @var $check CheckerInterface
         */
        foreach ($checkList as $check) {
            $check->execute();
        }

    }


    public function setClassName($className)
    {
        $this->className = $className;
    }

    public function getClassName()
    {
        return $this->className;
    }


    public function setCurrentNamespace($namespace)
    {
        $this->currentNamespace = $namespace;
    }

    public function getCurrentNamespace()
    {
        return $this->currentNamespace;
    }

    public function getContent()
    {
        return $this->file->getContents();
    }

    public function addUsesNamespaces($namespace)
    {
        $this->usesNamespaces[] = $namespace;
    }

    public function getUsesNamespaces()
    {
        return $this->usesNamespaces;
    }

    public function addInstantiation($instantiations)
    {
        $this->instantiations[] = $instantiations;
    }

    public function getInstantiations()
    {
        return $this->instantiations;
    }

    /**
     * /home/abc/projects/legacy/something/dirty.php => something/dirty.php
     *
     *
     * @return string
     */
    protected function getAutoloadPath()
    {
        return substr($this->realPath, strlen($this->basePath));
    }

    public function __toString()
    {
        $str = '----------------------------------------' . PHP_EOL . 'File: ' . $this->realPath . PHP_EOL
             . 'Class:' . $this->className . PHP_EOL
             . 'Namespace:' . $this->currentNamespace . PHP_EOL
             . 'Autoloader:' . $this->getAutoloadPath(). PHP_EOL
             . 'Uses:' . PHP_EOL;

        foreach ($this->getUsesNamespaces() as $namespace) {
            $str .= "\t" . $namespace->name . ' => ' . $namespace->alias . PHP_EOL;
        }

        $str .= 'Instantiations:' . PHP_EOL;

        foreach ($this->getInstantiations() as $instantiation) {
            $str .= "\t" . $instantiation->class . PHP_EOL;
        }

        return $str;
    }

}
