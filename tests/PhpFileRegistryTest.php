<?php

namespace Certi\LegacypsrFour\Tests;

use Certi\LegacypsrFour\Item\Classes;
use Certi\LegacypsrFour\Item\Instantation;
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

    /**
     * @test
     */
    public function addIntoFileRegistryTestTheSameClassnameDiffNamespaces()
    {
        // @todo: fix it!
        $this->markTestIncomplete('Error! fix it!');

        $registry = new PhpFileRegistry();

        $methods = [
            'getID'                       => 'abc123',
            'getClassName'                => 'Foo',
            'getCorrectNamespaceForClass' => 'Legacy\\Code',
        ];

        $mock = Tests\Helper::getFileMock([], $methods);
        $registry->addFile($mock);

        $methods = [
            'getID'                       => 'abc123',
            'getClassName'                => 'Foo',
            'getCorrectNamespaceForClass' => 'Cool\\Type',
        ];


        $mock = Tests\Helper::getFileMock([], $methods);
        $registry->addFile($mock);

    }

    public function dataProviderForIsGlobalScopeInstantationTest()
    {
        $caseList = [];

        $caseList[] = [
            'name' => 'SomeAwsomeClass',
            'expected' => false,
        ];

        $caseList[] = [
            'name' => 'One\\More\\Time',
            'expected' => false,
        ];

        $caseList[] = [
            'name' => '\\One\\More\\Time',
            'expected' => true,
        ];

        $caseList[] = [
            'name' => '\\Exception',
            'expected' => true,
        ];

        return $caseList;
    }

    /**
     * @test
     *
     * @dataProvider dataProviderForIsGlobalScopeInstantationTest
     */
    public function isGlobalScopeInstantationTest($name, $expected)
    {
        $registry     = new PhpFileRegistry();
        $instantation = new Instantation();
        $instantation->setName($name);

        $this->assertEquals($expected, $registry->isGlobalScopeInstantation($instantation));
    }


    public function dataProviderForIsClassDuplicatedTest()
    {
        $caseList = [];

        # 0 diff
        $caseList[] = [
            'list' => [
                [
                    'name'      => 'Abc',
                    'namespace' => 'Foo',
                ],
                [
                    'name'      => 'AbcNew',
                    'namespace' => 'Bar',
                ],
            ],
            'expected' => [
                false,
                false,
            ]
        ];

        # 1 classname the same, diff namespaces
        $caseList[] = [
            'list' => [
                [
                    'name'      => 'Abc',
                    'namespace' => 'Foo',
                ],
                [
                    'name'      => 'Abc',
                    'namespace' => 'Bar',
                ],
            ],
            'expected' => [
                false,
                false,
            ]
        ];

        # 2
        # namespaces the same, diff classes
        $caseList[] = [
            'list' => [
                [
                    'name'      => 'Abc',
                    'namespace' => 'Foo',
                ],
                [
                    'name'      => 'Xyc',
                    'namespace' => 'Foo',
                ],
            ],
            'expected' => [
                false,
                false,
            ]
        ];

        $caseList = [];
        # 3
        # classnames and namespaces the same
        $caseList[] = [
            'list' => [
                [
                    'name'      => 'Abc',
                    'namespace' => 'Foo',
                ],
                [
                    'name'      => 'Abc',
                    'namespace' => 'Foo',
                ],
            ],
            'expected' => [
                false,
                true,
            ]
        ];

        return $caseList;
    }

    /**
     * @test
     *
     * @dataProvider dataProviderForIsClassDuplicatedTest
     */
    public function isClassDuplicatedTest($list, $expected)
    {
        $registry = new PhpFileRegistry();

        $isClassDuplicated = new \ReflectionMethod('Certi\LegacypsrFour\PhpFileRegistry', 'isClassDuplicated');
        $isClassDuplicated->setAccessible(true);

        $insertIntoRegistry = new \ReflectionMethod('Certi\LegacypsrFour\PhpFileRegistry', 'insertIntoClassRegistry');
        $insertIntoRegistry->setAccessible(true);

        for ($i = 0; $i < count($list); ++$i) {

            $item = $list[$i];
            $itemClass = new Classes();
            $itemClass->setName($item['name']);
            $itemClass->setNamespace($item['namespace']);

            $res = $isClassDuplicated->invoke($registry, $itemClass);

            $this->assertEquals($expected[$i], $res);

            $insertIntoRegistry->invoke($registry, $itemClass);
        }


    }

}
