<?php

namespace Certi\LegacypsrFour\Checker;

class GetInstantiations extends CheckerAbstract
{
    public function execute()
    {
        $regexp = '/(=|\(|\s)new\s+([^[\(|\s|\$]*]*)(\s*|[;])/im';
        if (preg_match_all($regexp, $this->getContent(), $matches)) {

            for ($i = 0; $i < count($matches[0]); $i++) {

                if (empty($matches[2][$i])) {
                    continue;
                }
                $instantiation = new \stdClass();
                $instantiation->class = $matches[2][$i];
                $this->file->addInstantiation($instantiation);

            }
            return true;
        }
        return false;

    }

}
