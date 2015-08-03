<?php

namespace Certi\LegacypsrFour\Tests;

use Certi\LegacypsrFour\PhpFileRegistry;
use Certi\LegacypsrFour\Tests;



class GetStaticCallsTest extends \PHPUnit_Framework_TestCase
{

    public function dataProviderForAddIntoFileRegistryTest()
    {
        $caseList = [
            [ #0
                'fileList' => [],
                'expected' => 0,
            ],

            [ #1
                'fileList' => [
                    [
                        'getRealPath' => '/home/abc/test1.php',
                        'getFileName' => 'test1.php',
                    ],
                ],
                'expected' => 1,
            ],

            [ #2
                'fileList' => [
                    [
                        'getRealPath' => '/home/abc/test1.php',
                        'getFileName' => 'test1.php',
                    ],
                    [
                        'getRealPath' => '/home/abc/test2.php',
                        'getFileName' => 'test2.php',
                    ],
                ],
                'expected' => 2,
            ]
        ];

        return $caseList;
    }

    /**
     * @param $fileList
     * @param $expected
     *
     * @test
     *
     * @dataProvider dataProviderForAddIntoFileRegistryTest
     */
    public function addIntoFileRegistryTest($fileList, $expected)
    {
        $registry = new PhpFileRegistry();
        foreach ($fileList as $fileData) {
            $mock = Tests\Helper::getFileMock($fileData);
            $registry->addFile($mock);
        }
        $this->assertEquals($expected, $registry->countFileRegistry());
    }

    /**
     * @test
     */
    public function addIntoFileRegistryTestExceptionOnDuplicated()
    {
        $this->setExpectedException('\Exception');

        $registry = new PhpFileRegistry();

        $mock = Tests\Helper::getFileMock();
        $registry->addFile($mock);

        $mock = Tests\Helper::getFileMock();
        $registry->addFile($mock);

    }

}