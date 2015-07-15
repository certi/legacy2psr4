<?php

namespace Certi\LegacypsrFour\Checker;

class GetNamespace extends CheckerAbstract
{

    public function execute()
    {
        // has namespace?
        if (preg_match('/namespace\s*(\S*);[\s|{]/', $this->getContent(), $matches)) {
            $this->file->setCurrentNamespace($matches[1]);
            return true;
        }
        return false;

    }

}
