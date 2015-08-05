<?php

namespace Certi\LegacypsrFour\Checker;

use Certi\LegacypsrFour\PhpFile;

abstract class CheckerAbstract implements CheckerInterface
{
    protected $file;

    /**
     * Req exp for signs before instantation
     *
     * $a = new Object()
     * foo( new Object())
     * foo($a, new Object()
     */
    const SPLITTER = '(^\s*|(=|\(|,)\s*)';

    public function __construct(PhpFile $file)
    {
        $this->file = $file;
    }

    abstract function execute();

    protected function getContent()
    {
        return $this->file->getOriginalContent();
    }

}
