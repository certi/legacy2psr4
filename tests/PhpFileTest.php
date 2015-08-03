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

}
