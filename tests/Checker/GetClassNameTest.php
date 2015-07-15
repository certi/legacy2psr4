<?php

namespace Certi\LegacypsrFour\Tests\Checker;

use Certi\LegacypsrFour\Tests;
use Certi\LegacypsrFour\Checker\GetClassName;


class GetClassNameTest extends \PHPUnit_Framework_TestCase
{

    public function dataProviderForExecuteTest()
    {
        $caseList = [
            [ #0
                'content'  => 'class Psr4Fixer {' . PHP_EOL ,
                'expected' => 'Psr4Fixer',
            ],
            [ #1
                'content'  => 'class Psr4Fixer ' . PHP_EOL . '{',
                'expected' => 'Psr4Fixer',
            ],
            [ #2
                'content'  => 'class MyPrettyClass {' . PHP_EOL ,
                'expected' => 'MyPrettyClass',
            ],
            [ #3
                'content'  => 'class MyPrettyClass' . PHP_EOL . '{',
                'expected' => 'MyPrettyClass',
            ],
            [ #4
                'content'  => 'class MyPrettyClass implements PonyHoffInterface' . PHP_EOL . '{',
                'expected' => 'MyPrettyClass',
            ],
            [ #5
                'content'  => '  class   My_Pretty_Class  implements  PonyHoffInterface ' . PHP_EOL . '{',
                'expected' => 'My_Pretty_Class',
            ],

            [ #6
                'content'  => '  class   My_Pretty_Class  implements  PonyHoffInterface ' . PHP_EOL . '{} class MyPrettyClass2{}'. PHP_EOL . '{} class MyPrettyClass3{}',
                'expected' => 'My_Pretty_Class',
            ],

            [ #7
                'content'  => 'abstract class Test ',
                'expected' => 'Test',
            ],

            [ #7
                'content'  => 'final class AbstractClassTest' . PHP_EOL . '{',
                'expected' => 'AbstractClassTest',
            ],

            ## negativ
            [ #?
                'content'  => 'var a = hasClass();',
                'expected' => null,
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

        $checker = new GetClassName($file);
        $checker->execute();

        $this->assertEquals($expected, $file->getClassName());

    }

}
