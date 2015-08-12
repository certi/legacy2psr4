<?php

namespace Certi\LegacypsrFour;


class PhpFileFixer
{
    protected $fileID;

    /**
     * @var PhpFile
     */
    protected $file;

    protected $registry;

    public function __construct($fileID, PhpFileRegistry $registry)
    {
        $this->fileID   = $fileID;
        $this->file     = $registry->getPhpFileById($fileID);
        $this->registry = $registry;
    }

    public function run()
    {
        // @todo: backup?
        $fixerList = [];
        $fixerList[] = new Fixer\NamespaceFixer($this->file, $this->registry);
        $fixerList[] = new Fixer\UsedNamespaceFixer($this->file, $this->registry);

        foreach ($fixerList as $fixer) {
            $fixer->run();
        }
    }
}
