<?php

namespace Certi\LegacypsrFour;

use Certi\LegacypsrFour\Item\Namespaces;
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

    protected $staticCalls = [];

    public function __construct(Finder\SplFileInfo $file, $basePath)
    {
        $this->file            = $file;
        $this->realPath        = $file->getRealPath();
        $this->currentFileName = $file->getFilename();
        $this->basePath        = realpath($basePath);
        $this->id              = sprintf('%x', crc32($this->getRealPath() . '#' . $file->getContents()));
    }

    /**
     * gets uniq id of this file
     *
     * @return string
     */
    public function getID() {
        return $this->id;
    }

    public function check()
    {

        $checkList   = [];
        $checkList[] = new Checker\GetClassName($this);
        $checkList[] = new Checker\GetNamespace($this);
        $checkList[] = new Checker\GetUsesNamespaces($this);
        $checkList[] = new Checker\GetInstantiations($this);
        $checkList[] = new Checker\GetStaticCalls($this);

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



    public function setCurrentNamespace(Namespaces $namespace)
    {
        $this->currentNamespace = $namespace;
    }

    /**
     * @return Namespaces
     */
    public function getCurrentNamespace()
    {
        return $this->currentNamespace;
    }

    public function getTargetNamespace()
    {
        return preg_replace('/', '\\', $this->getAutoloadPath());
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

    public function addStaticCall($statiCalls)
    {
        $this->staticCalls[] = $statiCalls;
    }

    public function getStaticCalls()
    {
        return $this->staticCalls;
    }

    public function getRealPath()
    {
        return $this->realPath;
    }

    /**
     * /home/abc/projects/legacy/something/dirty.php => something/dirty.php
     *
     *
     * @return string
     */
    public function getAutoloadPath()
    {
        return substr($this->realPath, strlen($this->basePath));
    }

    public function __toString()
    {
        $str = '----------------------------------------' . PHP_EOL
             . 'File: ' . $this->realPath . ' (ID:' . $this->getID() . ')' . PHP_EOL
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

        $str .= 'StaticCalls:' . PHP_EOL;
        foreach ($this->getStaticCalls() as $staticCalls) {
            $str .= "\t" . $staticCalls->class . PHP_EOL;
        }

        return $str;
    }


    /**
     * Injects new content
     *
     * @param string  $content
     * @param integer $position
     *
     * @return string
     */
    public function inject($content, $position)
    {
        $array  = preg_split("/\n/", $this->getContent());
        $begin  = array_slice($array, 0, $position);
        $inject = array($content);
        $end    = array_slice($array, $position, count($array) - $position);

        $res = array_merge($begin, $inject, $end);

        $array = array_unique($res);
        return implode(PHP_EOL, $array);
    }

    /**
     * Replaces the whole line with new content
     *
     * @param string  $newContent
     * @param integer $position
     *
     * @return string
     */
    public function replace($newContent, $position)
    {
        $array  = preg_split("/" . PHP_EOL . "/", $this->getContent());
        $array[$position] = $newContent;
        return implode(PHP_EOL, $array);
    }

}
