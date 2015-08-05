<?php

namespace Certi\LegacypsrFour\Checker;

class GetInstantiations extends CheckerAbstract
{
    public function execute()
    {
        $regexp = '/' . self::SPLITTER . 'new\s+([^[\(|\s|\$]*]*)(\s*|[;])/im';
        if (preg_match_all($regexp, $this->getContent(), $matches)) {

            for ($i = 0; $i < count($matches[0]); $i++) {

                if (empty($matches[3][$i])) {
                    continue;
                }
                $instantiation = new \stdClass();
                $instantiation->class = $matches[3][$i];
                $this->file->addInstantiation($instantiation);

            }
            return true;
        }
        return false;

    }

}
