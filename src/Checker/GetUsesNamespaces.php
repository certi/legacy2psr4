<?php

namespace Certi\LegacypsrFour\Checker;

class GetUsesNamespaces extends CheckerAbstract
{
    public function execute()
    {
        $regexp = '/^use\s*(\S*)(\s+as\s*(\S+))?;/im';
        if (preg_match_all($regexp, $this->getContent(), $matches)) {

            for ($i = 0; $i < count($matches[0]); $i++) {

                $uses = new \stdClass();
                $uses->name  = $matches[1][$i];
                if (!empty($matches[3][$i])) {
                    $uses->alias = $matches[3][$i];
                } else {
                    $uses->alias = null;
                }

                $this->file->addUsesNamespaces($uses);

            }


            return true;
        }
        return false;

    }

}

__halt_compiler();

preg_match_all => array (
0 =>
    array (
        0 => 'use Abc\\Def\\Ghi as Xyz;',
        1 => 'use Jkl\\Mno as Prs;',
        2 => 'use Prs\\Tuw;',
    ),
1 =>
    array (
        0 => 'Abc\\Def\\Ghi',
        1 => 'Jkl\\Mno',
        2 => 'Prs\\Tuw',
    ),
2 =>
    array (
        0 => ' as Xyz',
        1 => ' as Prs',
        2 => '',
    ),
3 =>
    array (
        0 => 'Xyz',
        1 => 'Prs',
        2 => '',
    ),
)
