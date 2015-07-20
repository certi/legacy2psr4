<?php

namespace Certi\LegacypsrFour\Checker;

class GetStaticCalls extends CheckerAbstract
{
    public function execute()
    {

        /*
        Object::method(123);
        Object::$var++;
        parent::doit();
        self::doSomething();
        self::$a++;
        */

        #$regexp = '/(=|\()\s([^[\(|\s|self|parent]*]*)::/im';
        $assign = '(=|\()';
        $regexp = '/' . $assign . '?\s*(\S*)::/im';
        if (preg_match_all($regexp, $this->getContent(), $matches)) {

            for ($i = 0; $i < count($matches[0]); $i++) {

                if (
                    empty($matches[2][$i])
                    || in_array($matches[2][$i], array('parent', 'self'))
                    || strpos($matches[2][$i], '$') !== false
                ) {
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
