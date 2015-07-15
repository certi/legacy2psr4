<?php

namespace Certi\LegacypsrFour\Tests;

use Certi\LegacypsrFour\PhpFile;

class Helper extends \PHPUnit_Framework_TestCase
{

    private static $instance = null;

    public static function getInstance()
    {

        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;

    }

    public static function getFileMock($fileParams = [])
    {

        $fileMethods = [
            'getRealPath' => '/home/projects/legacypsrfour/Checker/Checker.php',
            'getFileName' => 'Checker.php',
            'getContents' => '<?php' . PHP_EOL . 'echo 1;' . PHP_EOL,
        ];

        // @todo: warning on unknown params
        foreach ($fileMethods as $method => $return) {
            if (isset($fileParams[$method])) {
                $fileMethods[$method] = $fileParams[$method];
            }
        }


        $mock = self::getInstance()->getMock('Symfony\Component\Finder\SplFileInfo', array_keys($fileMethods), [], '', false);
        foreach ($fileMethods as $method => $return) {
            $mock->method($method)->willReturn($return);
        }

        $file = new PhpFile($mock, '');
        return $file;
    }

}
