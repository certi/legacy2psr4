<?php

namespace Certi\LegacypsrFour\Tests;

use Certi\LegacypsrFour\Tests;

class PhpFileTest extends \PHPUnit_Framework_TestCase {

   public function dataProviderForInject()
   {

       $caseList = [];

       $caseList[] = [
           'content'   => '<?php' . PHP_EOL,
           'injection' => 'xyz',
           'position'  => 1,
           'expected'  => '<?php' . PHP_EOL . 'xyz' . PHP_EOL,
       ];

       return $caseList;
   }

    /**
     *
     * @test
     *
     * @param $injection
     * @param $position
     * @param $content
     * @param $expected
     *
     * @dataProvider dataProviderForInject
     */
    public function injectTest($content, $injection, $position, $expected)
    {
        $file = Tests\Helper::getFileMock(['getContents' => $content]);
        $res  = $file->inject($injection, $position);
        $this->assertEquals($expected, $res);
    }



    public function dataProviderForReplaceTest()
    {
        $caseList = [];

        $caseList[] = [
            'content'    => '<?php' . PHP_EOL . PHP_EOL . 'namespace Abc;' . PHP_EOL,
            'newContent' => 'namespace Xyz;',
            'position'   => 2,
            'expected'   => '<?php' . PHP_EOL . PHP_EOL . 'namespace Xyz;' . PHP_EOL,
        ];

        return $caseList;

    }

    /**
     * @test
     *
     * @param $content
     * @param $newContent
     * @param $position
     * @param $expected
     *
     * @dataProvider dataProviderForReplaceTest
     */
    public function replaceTest($content, $newContent, $position, $expected)
    {
        $file = Tests\Helper::getFileMock(['getContents' => $content]);
        $res  = $file->replace($newContent, $position);
        $this->assertEquals($expected, $res);
    }

    public function dataProviderForGetAutoloadPathTest()
    {
        $caseList = [];

        $caseList[] = [
            'path'     => '/home/i/Fixer/UsedNamespaceFixer.php',
            'base'     => '/home/i/',
            'expected' => 'Fixer/UsedNamespaceFixer.php',
        ];

        return $caseList;
    }

    /**
     * @tests
     *
     * @dataProvider dataProviderForGetAutoloadPathTest
     */
    public function getAutoloadPathTest($path, $base, $expected)
    {
        $mockParams = [
            'getRealPath' => $path,
            'getBasePath' => $base,
        ];

        $file = Tests\Helper::getFileMock([], $mockParams);
        $this->assertEquals($expected, $file->getAutoloadPath());
    }

    public function dataProviderForGetTargetNamespaceTest()
    {
        $caseList = [];
        $caseList[] = [
            'path'     => '/home/i/Fixer/UsedNamespaceFixer.php',
            'base'     => '/home/i/',
            'expected' => 'Fixer\UsedNamespaceFixer',
        ];

        $caseList[] = [
            'path'     => '/home/i/Fixer/UsedNamespaceFixer.class..php',
            'base'     => '/home/i/',
            'expected' => 'Fixer\UsedNamespaceFixer',
        ];
        $caseList[] = [
            'path'     => '/home/i/Fixer/UsedNamespace_1Fixer.class.php',
            'base'     => '/home/i/',
            'expected' => 'Fixer\UsedNamespace_1Fixer',
        ];

        return $caseList;
    }

    /**
     * @tests
     *
     * @dataProvider dataProviderForGetTargetNamespaceTest
     *
     * @depends getAutoloadPathTest
     */
    public function getTargetNamespaceTest($path, $base, $expected)
    {

        $fileParams = [
            'getRealPath' => $path,
            'getBasePath' => $base,
        ];

        $file = Tests\Helper::getFileMock([], $fileParams);
        $this->assertEquals($expected, $file->getTargetNamespace());
    }

}
