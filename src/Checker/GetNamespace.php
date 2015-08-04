<?php

namespace Certi\LegacypsrFour\Checker;

use Certi\LegacypsrFour\Item\Namespaces;

class GetNamespace extends CheckerAbstract
{
    public function execute()
    {

        foreach ($this->file->getCurrentContentArray() as $index => $content) {

            // has namespace?
            // $reg = '/namespace\s*(\S*);[\s|{]/';
            $reg = '/^namespace\s*(\S*)\s*;/';

            if (preg_match($reg, $content, $matches)) {

                $namespace = new Namespaces();
                $namespace->setName($matches[1]);
                $namespace->setIndex($index);

                $this->file->addCurrentNamespaces($namespace);

            }

        }

        if (count($this->file->getCurrentNamespaces())) {
            return true;
        }
        return false;

    }

}
