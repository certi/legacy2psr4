<?php

namespace Certi\LegacypsrFour\Fixer;

use Certi\LegacypsrFour\PhpFile;
use Certi\LegacypsrFour\PhpFileRegistry;

class NamespaceFixer extends AbstractFixer
{

    /**
     * Adds or replaces the namespace.
     */
    public function run()
    {
        if (!$this->file->getCurrentNamespaces()) {
            $this->addNamespace();
        } elseif (false === $this->file->isNamespaceCorrect()) {
            $this->replaceNamespace();
        }

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
