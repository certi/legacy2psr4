<?php

namespace Certi\LegacypsrFour\Fixer;

use Certi\LegacypsrFour\PhpFile;
use Certi\LegacypsrFour\PhpFileRegistry;

abstract class AbstractFixer
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
     * Fixes the file
     */
    abstract public function run();

}
