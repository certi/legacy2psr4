<?php

namespace Certi\LegacypsrFour\Tests\Checker;

use Certi\LegacypsrFour\Tests;
use Certi\LegacypsrFour\Checker\GetNamespace;


class GetNamespaceTest extends \PHPUnit_Framework_TestCase
{

    public function dataProviderForExecuteTest()
    {
        $caseList = [
            [ #0
                'content'   => 'namespace Abc;' . PHP_EOL,
                'expectedC' => 1,
                'expectedN' => 'Abc',
            ],
            [ #1
                'content'   => 'namespace Abc\Def\Ghi;' . PHP_EOL ,
                'expectedC' => 1,
                'expectedN' => 'Abc\Def\Ghi',
            ],
            [ #2
                'content'   => '$reg = \'/namespace\s*(\S*);[\s|{]/\'',
                'expectedC' => 0,
                'expectedN' => null,
            ],

            //
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
    public function executeTest($content, $expectedCount, $expectedName)
    {

        $file = Tests\Helper::getFileMock(['getContents' => $content]);

        $checker = new GetNamespace($file);
        $checker->execute();

        $this->assertCount($expectedCount, $file->getCurrentNamespaces());
        if ($expectedCount == 1) {
            $this->assertEquals($expectedName, $file->getCurrentNamespaces()[0]->getName());
        }

    }

}
