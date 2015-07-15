<?php

namespace Certi\LegacypsrFour\Checker;

class GetClassName extends CheckerAbstract
{
    public function execute()
    {
        $regexp = '/(final|abstract)?\s*class\s*(\S*)[\s|\n|{]/m';

        if (preg_match($regexp, $this->getContent(), $matches)) {
            $this->file->setClassName($matches[2]);
            return true;
        }
        return false;

    }
}
