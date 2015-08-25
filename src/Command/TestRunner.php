<?php

namespace Certi\LegacypsrFour\Command;

use Certi\LegacypsrFour\Checker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Finder;
use PHPUnit_Framework_TestCase as Phpunit;

class TestRunner implements \PHPUnit_Framework_Test
{
    const FUNCTIONAL_TEST_IN_DIR      = 'in';
    const FUNCTIONAL_TEST_OUT_DIR     = 'out';
    const FUNCTIONAL_TEST_COMPARE_DIR = 'compare';

    protected $params = [];
    protected $count  = 1;

    public function __construct($params)
    {
        $this->params = $params;
    }

    public function count() {
        return $this->count;
    }

    public function run(\PHPUnit_Framework_TestResult $result = null)
    {
        if ($result === null) {
            $result = new \PHPUnit_Framework_TestResult();
        }

        $dirList = $this->params['dirList'];

        $fixer = new Fixer();
        $base  = 'Abc\\Def';
        $out   = [];

        foreach ($dirList as $dir)
        {

            $result->startTest($this);

            \PHP_Timer::start();
            $stopTime = null;

            $dir .= DIRECTORY_SEPARATOR;

            $inputDirectory   = $dir . self::FUNCTIONAL_TEST_IN_DIR;
            $outputDirectory  = $dir . self::FUNCTIONAL_TEST_OUT_DIR;
            $compareDirectory = $dir . self::FUNCTIONAL_TEST_COMPARE_DIR;

            $out[] = 'DIR: ' . $inputDirectory;

            $this->clearDirectory($outputDirectory);

            $res = $fixer->doit($inputDirectory, $base, $outputDirectory);

            $out[] = $res;

            try {
                $this->checkResults($outputDirectory, $compareDirectory);
            } catch (\PHPUnit_Framework_AssertionFailedError $e) {
                $stopTime = \PHP_Timer::stop();
                $result->addFailure($this, $e, $stopTime);
            } catch (\Exception $e) {
                $stopTime = \PHP_Timer::stop();
                $result->addError($this, $e, $stopTime);
            }

            if ($stopTime === null) {
                $stopTime = \PHP_Timer::stop();
            }

            $result->endTest($this, $stopTime);

            // $this->clearDirectory($outputDirectory);
        }
        return $result;

    }

    protected function clearDirectory($directory)
    {

        if (!file_exists($directory)) {
            mkdir($directory, 0775);
            return;
        }

        $f = new Finder\Finder();
        $f->ignoreDotFiles(true)
            ->in($directory);

        foreach ($f as $fil) {
            if (is_dir($fil)) {
                rmdir($fil);
                continue;
            }
            unlink($fil);
        }

    }

    protected function checkResults($outputDirectory, $compareDirectory)
    {
        /**
         * @var Finder\SplFileInfo[] $outputFiles
         */
        $outputFiles                 = [];
        $outputSplFileInfoCollection = $this->getFilesBBB($outputDirectory);
        foreach ($outputSplFileInfoCollection as $oFile) {
            $outputFiles[] = $oFile;
        }

        /**
         * @var Finder\SplFileInfo[] $compareFiles
         */
        $compareFiles                 = [];
        $compareSplFileInfoCollection = $this->getFilesBBB($compareDirectory);;
        foreach ($compareSplFileInfoCollection as $oFile) {
            $compareFiles[] = $oFile;
        }
        Phpunit::assertCount(count($compareFiles), $outputFiles);

        for ($i = 0; $i < count($outputDirectory); ++$i) {

            $oFile = $outputFiles[$i];
            $cFile = $compareFiles[$i];

            Phpunit::assertFileEquals($cFile->getRealPath(), $oFile->getRealPath());
        }

        // check content.
    }

    /**
     * @param $path
     *
     * @return Finder
     */
    protected function getFilesBBB($path)
    {
        $finder = new Finder\Finder();
        $finder
            ->files()
            ->in($path)
            ->ignoreDotFiles(true)
        ;
        return $finder;
    }

}
