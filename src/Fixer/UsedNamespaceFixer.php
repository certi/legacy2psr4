<?php

namespace Certi\LegacypsrFour\Fixer;

use Certi\LegacypsrFour\PhpFile;
use Certi\LegacypsrFour\PhpFileRegistry;

class UsedNamespaceFixer
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

    }

}
