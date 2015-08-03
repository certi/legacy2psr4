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
                'content'  => 'namespace Abc;' . PHP_EOL ,
                'expected' => 'Abc',
            ],

            [ #1
                'content'  => 'namespace Abc\Def\Ghi;' . PHP_EOL ,
                'expected' => 'Abc\Def\Ghi',
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

        $checker = new GetNamespace($file);
        $checker->execute();

        $this->assertEquals($expected, $file->getCurrentNamespace()->getName());

    }

}
