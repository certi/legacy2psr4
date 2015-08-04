<?php

namespace Certi\LegacypsrFour\Fixer;

use Certi\LegacypsrFour\PhpFile;
use Certi\LegacypsrFour\PhpFileRegistry;

class NamespaceFixer
{
    /**
     * @var PhpFile
     */
    protected $file;

    /**
     * @var PhpFileRegistry
     */
    protected $registry;

    public function __construct(PhpFile $file, PhpFileRegistry $registry)
    {
        $this->file = $file;
        $this->registry = $registry;
    }

    public function run()
    {
        if (!$this->file->getCurrentNamespaces()) {
            $this->addNamespace();
        } elseif ($this->file->getCurrentNamespaces()[0]->getName() != $this->file->getTargetNamespace()) {
            $this->replaceNamespace();
        }

    }

    protected function addNamespace()
    {
        $line = PHP_EOL
              . 'namespace ' . $this->file->getTargetNamespace() . ';'
              . PHP_EOL
              ;

        $this->file->inject($line, 2);
    }


    protected function replaceNamespace()
    {

        $line = PHP_EOL
              . 'namespace ' . $this->file->getTargetNamespace() . ';'
              . PHP_EOL;

        $this->file->replace($line, $this->file->getCurrentNamespaces()[0]->getIndex());
    }

}
