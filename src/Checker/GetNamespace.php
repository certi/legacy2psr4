<?php

namespace Certi\LegacypsrFour\Checker;

use Certi\LegacypsrFour\Item\Namespaces;

class GetNamespace extends CheckerAbstract
{

    public function execute()
    {
        $namespace = new Namespaces();


        // has namespace?
        if (preg_match('/namespace\s*(\S*);[\s|{]/', $this->getContent(), $matches)) {

            $namespace->setName($matches[1]);
            $namespace->setLine(100);// dummy

            $this->file->setCurrentNamespace($namespace);
            return true;
        }
        return false;

    }

}
