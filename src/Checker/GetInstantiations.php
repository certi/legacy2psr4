<?php

namespace Certi\LegacypsrFour\Checker;

class GetInstantiations extends CheckerAbstract
{
    public function execute()
    {
        $regexp = '/' . self::SPLITTER . 'new\s+([^[\(|\s|\$|,]*]*)(\s*|;|,)/i';

        foreach ($this->file->getCurrentContentArray() as $index => $content) {

            if (preg_match_all($regexp, $content, $matches)) {

                for ($i = 0; $i < count($matches[0]); $i++) {

                    if (empty($matches[3][$i])) {
                        continue;
                    }
                    $instantiation        = new \stdClass();
                    $instantiation->name  = $matches[3][$i];
                    $instantiation->index = $index;

                    $this->file->addInstantiation($instantiation);

                }
            }
        }

        if (count($this->file->getInstantiations())) {
            return true;
        }
        return false;
    }

}

