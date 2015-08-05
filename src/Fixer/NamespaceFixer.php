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

    /**
     * Adds or replaces the namespace.
     */
    public function run()
    {
        if (!$this->file->getCurrentNamespaces()) {
            $this->addNamespace();
        } elseif (false === $this->isNamespaceCorrect()) {
            $this->replaceNamespace();
        }

    }

    /**
     * Is the current Namespace correct?
     *
     * @todo: move it into PhpFile
     *
     * @return bool
     */
    protected function isNamespaceCorrect() {
        if (0 == count($this->file->getCurrentNamespaces())) {
            return false;
        }
        return $this->file->getCurrentNamespaces()[0]->getName() === $this->file->getTargetNamespace();
    }

    /**
     * Adds namespace at start of the file
     */
    protected function addNamespace()
    {
        $line = PHP_EOL
              . 'namespace ' . $this->file->getTargetNamespace() . ';'
              . PHP_EOL
              ;

        $this->file->inject($line, 2);
    }

    /**
     * Replaces namespace (because of )
     */
    protected function replaceNamespace()
    {
        $currentNamespace = $this->file->getCurrentNamespaces()[0];

        $line = PHP_EOL
              . 'namespace ' . $this->file->getTargetNamespace() . ';'
              . PHP_EOL;

        $this->file->replace($line, $currentNamespace->getIndex());
    }

}
