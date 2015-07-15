<?php

namespace Certi\LegacypsrFour\Checker;

use Certi\LegacypsrFour\PhpFile;

interface CheckerInterface
{
    public function __construct(PhpFile $file);

    public function execute();

}
