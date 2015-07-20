<?php

namespace Certi\LegacypsrFour\Tests\Checker;

use Certi\LegacypsrFour\Checker\GetStaticCalls;
use Certi\LegacypsrFour\Tests;



class GetStaticCallsTest extends \PHPUnit_Framework_TestCase
{
    public function dataProviderForExecuteTest()
    {
        $caseList = [
            [ #0
                'content'  => '$res = Booking::cancel();' . PHP_EOL ,
                'expected' => [
                    [
                        'class' => 'Booking',
                    ],
                ],
            ],

            [ #1 // !
                'content'  => '$res::cancel();' . PHP_EOL ,
                'expected' => [],
            ],

            [ #2
                'content'  => 'Booking::$aaa;' . PHP_EOL ,
                'expected' => [
                    [
                        'class' => 'Booking',
                    ],
                ],
            ],

            [ #3
                'content'  => 'Booking::$aaa++;' . PHP_EOL ,
                'expected' => [
                    [
                        'class' => 'Booking',
                    ],
                ],
            ],

            [ #4
                'content'  => 'parent::doSomething();' . PHP_EOL ,
                'expected' => [],
            ],

            [ #5
                'content'  => 'self::doSomething();' . PHP_EOL ,
                'expected' => [],
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

        $checker = new GetStaticCalls($file);
        $checker->execute();

        $staticCalls = $file->getStaticCalls();

        $this->assertEquals(count($expected), count($staticCalls), 'Found:' . print_r($staticCalls, true));
        for ($i = 0; $i < count($expected); $i++) {
            $this->assertEquals($expected[$i]['class'], $staticCalls[$i]->class);
        }

    }

}
