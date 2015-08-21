<?php

namespace Certi\LegacypsrFour;

use Certi\LegacypsrFour\Item\Instantation;
use Certi\LegacypsrFour\Item\Namespaces;
use Symfony\Component\Finder;

class PhpFile
{
    const NS_SEPARATOR = '\\';

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

    /**
     * @var Instantation[]
     */
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
        // avoid double "/" ?? not really needed because of realpath
        $path = preg_replace('/\/{2,}/', '/', $this->getAutoloadPath());

        // delete file-extension
        $path = preg_replace('/(\..*)$/i', '', $path);

        // convert "/" in "\"
        $path = preg_replace('#/#', self::NS_SEPARATOR, $path);

        return $path;
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

    public function addInstantiation(Instantation $instantiations)
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

    public function getBasePath()
    {
        return $this->basePath;
    }

    /**
     * /home/abc/projects/legacy/something/dirty.php => something/dirty.php
     *
     *
     * @return string
     */
    public function getAutoloadPath()
    {

        $res = substr($this->getRealPath(), strlen($this->getBasePath()));
        return $res;
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
            $str[] = "\t" . $instantiation;
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
     * @todo: very important: increase the indexes of instantations etc.
     *
     * @param string  $content
     * @param integer $position
     *
     * @return string
     */
    public function inject($content, $position)
    {
        $begin  = array_slice($this->currentContentArray, 0, $position);
        $inject = array($content);
        $end    = array_slice($this->currentContentArray, $position, count($this->currentContentArray) - $position);

        $res = array_merge($begin, $inject, $end);

        $this->currentContentArray = array_unique($res);

        $this->reloadCurrentContentRaw();
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
        $this->currentContentArray[$position] = $newContent;
        $this->reloadCurrentContentRaw();
    }

    protected function reloadCurrentContentRaw()
    {
        $this->currentContentRaw = implode(PHP_EOL, $this->currentContentArray);
    }


    /**
     * Is the current Namespace correct?
     *
     * @todo: move it into PhpFile
     *
     * @return bool
     */
    public function isNamespaceCorrect()
    {
        if (0 == count($this->getCurrentNamespaces())) {
            return false;
        }
        return $this->getCurrentNamespaces()[0]->getName() === $this->getTargetNamespace();
    }

    public function getCorrectNamespaceForClass()
    {
        // get correct namespace
        if ($this->isNamespaceCorrect()) {
            $ns = $this->getCurrentNamespaces()[0];
        } else {
            $ns = $this->getTargetNamespace();
        }
        return $ns;
    }

    public function persist($targetBaseDir = null)
    {
        if (empty($targetBaseDir)) {
            $targetBaseDir = $this->file->getRealPath();
        }
        $targetFilePath = $targetBaseDir . DIRECTORY_SEPARATOR . $this->file->getRelativePathname();

        file_put_contents($targetFilePath, $this->getCurrentContentRaw());
    }

}
