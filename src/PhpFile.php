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

    protected $currentContentRaw;

    protected $currentContentArray = [];

    protected $currentFileName;

    protected $realPath;

    protected $basePath;

    protected $className;

    protected $currentNamespaces = [];

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

        $this->currentContentRaw  = $file->getContents();

        // @todo: detect file-specific EOL and use it to split the content. workaround:
        $this->currentContentArray = preg_split('/' . PHP_EOL . '/', $this->currentContentRaw);

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
        $checkList[] = new Checker\GetUsedNamespaces($this);
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


    public function addCurrentNamespaces(Namespaces $namespace)
    {
        $this->currentNamespaces[] = $namespace;
        if (count($this->currentNamespaces) > 1) {
            var_dump($this->currentNamespaces);
            throw new \Exception('Cannot fix files with multiple namespaces. Sorry :(');
        }
    }

    /**
     * @return Namespaces
     */
    public function getCurrentNamespaces()
    {
        return $this->currentNamespaces;
    }

    public function getTargetNamespace()
    {
        return preg_replace('#/#', '\\', $this->getAutoloadPath());
    }

    /**
     * @return string
     */
    public function getOriginalContent()
    {
        return $this->file->getContents();
    }

    public function getCurrentContentRaw()
    {
        return $this->currentContentRaw;
    }

    public function getCurrentContentArray()
    {
        return $this->currentContentArray;
    }

    public function addUsesNamespaces($namespace)
    {
        $this->usesNamespaces[] = $namespace;
    }

    public function getUsesNamespaces()
    {
        return $this->usesNamespaces;
    }

    /**
     * @todo type hinting.
     * @param $instantiations
     */
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
        $str = [];

        $str[] = '----------------------------------------';
        $str[] = 'File: ' . $this->realPath . ' (ID:' . $this->getID() . ')';
        $str[] = 'Autoloader:' . $this->getAutoloadPath();
        $str[] = 'Class:' . $this->className;
        $str[] = 'Namespace: ';
        foreach ($this->getCurrentNamespaces() as $currentNamespace) {
            $str[] = "\t" . $currentNamespace;
        }

        $str[] = 'Uses:';

        foreach ($this->getUsesNamespaces() as $namespace) {
            $str[] = "\t" . $namespace->name . ' => ' . $namespace->alias;
        }

        $str[] = 'Instantiations:';
        foreach ($this->getInstantiations() as $instantiation) {
            $str[] = "\t" . $instantiation->name . ' (' . $instantiation->index . ')';
        }

        $str[] = 'StaticCalls:';
        foreach ($this->getStaticCalls() as $staticCalls) {
            $str[] = "\t" . $staticCalls->class;
        }

        return implode(PHP_EOL, $str);
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
        $array  = preg_split("/\n/", $this->getOriginalContent());
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
        $array  = preg_split("/" . PHP_EOL . "/", $this->getOriginalContent());
        $array[$position] = $newContent;
        return implode(PHP_EOL, $array);
    }

}
