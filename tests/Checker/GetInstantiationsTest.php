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
                        'class' => 'Booking',
                    ]
                ]
            ],
            [ #1
                'content'  => '$a = new Booking(23123);' . PHP_EOL ,
                'expected' => [
                    [
                        'class' => 'Booking',
                    ],
                ]
            ],

            [ #2
                'content'  => ' $a = new Booking(23123) ; ' . PHP_EOL ,
                'expected' => [
                    [
                        'class' => 'Booking',
                    ],
                ]
            ],

            [ #3
                'content'  => '$a = new Booking($adsasd);' . PHP_EOL ,
                'expected' => [
                    [
                        'class' => 'Booking',
                    ],
                ]
            ],

            [ #4
                'content'  => '$x =func($b, new Booking(), $a);' . PHP_EOL ,
                'expected' => [
                    [
                        'class' => 'Booking',
                    ],
                ]
            ],

            [ #5
                'content'  => '$x =func($b, new Booking_Unterscore_1(), $a);' . PHP_EOL ,
                'expected' => [
                    [
                        'class' => 'Booking_Unterscore_1',
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
            $this->assertEquals($expected[$i]['class'], $instantations[$i]->class);
        }

    }

}
