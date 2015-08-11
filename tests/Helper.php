<?php

namespace Certi\LegacypsrFour\Tests;

use Certi\LegacypsrFour\PhpFile;
use Symfony\Component\Finder\SplFileInfo;

class Helper extends \PHPUnit_Framework_TestCase
{

    private static $instance = null;

    /*
     * We need the instance because getMock of Phpunit is not static.
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Gets mocked PhpFile to make filesystem indepedent tests
     *
     * @param array $splFileInfoParams
     * @param array $phpFileParams
     *
     * @return PhpFile
     */
    public static function getFileMock(array $splFileInfoParams = [], array $phpFileParams = [])
    {
        $splFileInfoMock = self::getSplFileInfoMock($splFileInfoParams);
        $phpFileMock     = self::getPhpFileMock($splFileInfoMock, $phpFileParams);
        return $phpFileMock;
    }

    /**
     *
     * @param array $fileParams
     * @return \PHPUnit_Framework_MockObject_MockObject | Symfony\Component\Finder\SplFileInfo
     */
    protected static function getSplFileInfoMock(array $fileParams = [])
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

        return $mock;
    }


    /**
     * @param $splFileInfoMock
     * @param $mockParams
     *
     * @return \PHPUnit_Framework_MockObject_MockObject | PhpFile
     */
    protected static function getPhpFileMock($splFileInfoMock, array $mockParams = [])
    {
        $basePath = isset($mockParams['getBasePath']) ? $mockParams['getBasePath'] : '';

        $mockDefaultParams = [
            'getBasePath' => '',
        ];
        $mockParams = array_merge($mockDefaultParams, $mockParams);

        $constructParams = [
            $splFileInfoMock,
            $basePath
        ];

        $mock = self::getInstance()->getMock('Certi\LegacypsrFour\PhpFile', array_keys($mockParams), $constructParams, '', true);


        foreach ($mockParams as $method => $return) {
            $mock->method($method)->willReturn($return);
        }

        return $mock;

    }
}
