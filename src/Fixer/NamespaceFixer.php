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
        if (null === $this->file->getCurrentNamespace()) {
            $this->addNamespace();
        } elseif ($this->file->getCurrentNamespace() != $this->file->getCurrentNamespace()) {
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
        throw new Exception('Not implemented yet');

        $line = PHP_EOL
              . 'namespace ' . $this->file->getTargetNamespace();

        if (null !== $this->file->getTargetNamespaceAlias()) {
            $line .= ' as ' . $this->file->getTargetNamespaceAlias();
        }

        $line .= ';'
              . PHP_EOL;

        $this->file->inject($line, 2);
    }

}
