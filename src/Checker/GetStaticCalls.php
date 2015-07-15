<?php

namespace Certi\LegacypsrFour\Checker;

class GetStaticCalls extends CheckerAbstract
{
    public function execute()
    {
        $regexp = '/(=|\()\s([^[\(|\s]*]*)::/im';
        if (preg_match_all($regexp, $this->getContent(), $matches)) {

            for ($i = 0; $i < count($matches[0]); $i++) {

                if (empty($matches[2][$i])) {
                    continue;
                }
                $calls        = new \stdClass();
                $calls->class = $matches[2][$i];
                $this->file->addStaticCall($calls);

            }
            return true;
        }
        return false;

    }

}
