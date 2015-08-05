<?php

namespace Certi\LegacypsrFour\Tests\Checker;

use Certi\LegacypsrFour\Tests;
use Certi\LegacypsrFour\Checker\GetUsedNamespaces;


class GetUsedNamespacesTest extends \PHPUnit_Framework_TestCase
{

    public function dataProviderForExecuteTest()
    {
        $caseList = [
            [ #0
                'content'  => 'use Abc\Def\Ghi;' . PHP_EOL ,
                'expected' => [
                    [
                        'namespace' => 'Abc\Def\Ghi',
                        'alias'     => null,
                    ]
                ]
            ],
            [ #1
                'content'  => 'use Abc\Def\Ghi as Xyz;' . PHP_EOL ,
                'expected' => [
                    [
                        'namespace' => 'Abc\Def\Ghi',
                        'alias'     => 'Xyz',
                    ],
                ]
            ],

            [ #2
                'content'  => 'use Abc\Def\Ghi as Xyz;' . PHP_EOL
                            . 'use Jkl\Mno as Prs;' . PHP_EOL
                            . 'use Prs\Tuw;' . PHP_EOL,
                'expected' => [
                    [
                        'namespace' => 'Abc\Def\Ghi',
                        'alias'     => 'Xyz',
                    ],
                    [
                        'namespace' => 'Jkl\Mno',
                        'alias'     => 'Prs',
                    ],
                    [
                        'namespace' => 'Prs\Tuw',
                        'alias'     => null,
                    ],
                ]
            ],
            [ #3
                'content'  => '  $session->get("usersettings_lang");' . PHP_EOL ,
                'expected' => []
            ],


        ];

        return $caseList;
    }


    /**
     * @param $content
     * @param $expected
     *
     * @test
     *
     * @dataProvider dataProviderForExecuteTest
     */
    public function executeTest($content, $expected)
    {

        $file = Tests\Helper::getFileMock(['getContents' => $content]);

        $checker = new GetUsedNamespaces($file);
        $checker->execute();

        $usesNamespaces = $file->getUsesNamespaces();

        $this->assertEquals(count($expected), count($usesNamespaces));
        for ($i = 0; $i < count($expected); $i++) {
            $this->assertEquals($expected[$i]['namespace'], $usesNamespaces[$i]->name);
            $this->assertEquals($expected[$i]['alias'], $usesNamespaces[$i]->alias);
        }

    }

}
