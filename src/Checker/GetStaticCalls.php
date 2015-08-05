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
        $regexp = '/' . self::SPLITTER . '((\w*))::/im';
        if (preg_match_all($regexp, $this->getContent(), $matches)) {

            for ($i = 0; $i < count($matches[0]); $i++) {

                if (
                    empty($matches[3][$i])
                    || in_array($matches[3][$i], array('parent', 'self'))
                    || strpos($matches[3][$i], '$') !== false
                ) {
                    continue;
                }

                $calls        = new \stdClass();
                $calls->class = $matches[3][$i];
                $this->file->addStaticCall($calls);

            }
            return true;
        }
        return false;

    }

}
