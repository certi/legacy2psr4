<?php

namespace Certi\LegacypsrFour\Tests\Checker;

use Certi\LegacypsrFour\Tests;
use Certi\LegacypsrFour\Checker\GetInstantiations;

class GetInstantiationsTest extends \PHPUnit_Framework_TestCase
{

    public function dataProviderForExecuteTest()
    {

        $caseList = [

            [ #0
                'content'  => '$a = new Booking();' . PHP_EOL ,
                'expected' => [
                    [
                        'name' => 'Booking',
                    ]
                ]
            ],
            [ #1
                'content'  => '$a = new Booking(23123);' . PHP_EOL ,
                'expected' => [
                    [
                        'name' => 'Booking',
                    ],
                ]
            ],

            [ #2
                'content'  => ' $a = new Booking(23123) ; ' . PHP_EOL ,
                'expected' => [
                    [
                        'name' => 'Booking',
                    ],
                ]
            ],

            [ #3
                'content'  => '$a = new Booking($adsasd);' . PHP_EOL ,
                'expected' => [
                    [
                        'name' => 'Booking',
                    ],
                ]
            ],

            [ #4
                'content'  => '$x =func($b, new Booking(), $a);' . PHP_EOL ,
                'expected' => [
                    [
                        'name' => 'Booking',
                    ],
                ]
            ],

            [ #5
                'content'  => '$x =func($b, new Booking_Unterscore_1(), $a);' . PHP_EOL ,
                'expected' => [
                    [
                        'name' => 'Booking_Unterscore_1',
                    ],
                ]
            ],


            [ #6
                'content'  => PHP_EOL . '$x =func(new Booking(), new Logger, $a);' . PHP_EOL ,
                'expected' => [
                    [
                        'name'  => 'Booking',
                        'index' => 1,
                    ],
                    [
                        'name'  => 'Logger',
                        'index' => 1,
                    ],
                ]
            ],

            [ # negativ
                'content'  => '$x =func($b, new $className(), $a);' . PHP_EOL ,
                'expected' => []
            ],

            [ # negativ
                'content'  => 'injects new content into something' . PHP_EOL ,
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

        $checker = new GetInstantiations($file);
        $checker->execute();

        $instantations = $file->getInstantiations();

        $this->assertEquals(count($expected), count($instantations));
        for ($i = 0; $i < count($expected); $i++) {
            $this->assertEquals($expected[$i]['name'], $instantations[$i]->name);
            if (isset($expected[$i]['index'])) {
                $this->assertEquals($expected[$i]['index'], $instantations[$i]->index);
            }
        }

    }

}
