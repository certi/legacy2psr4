<?php

namespace Certi\LegacypsrFour;

class PhpFileFixer
{

    protected $registry;

    public function __construct(PhpFileRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function run()
    {

    }

}
