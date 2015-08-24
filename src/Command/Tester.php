<?php

namespace Certi\LegacypsrFour\Command;

use Certi\LegacypsrFour\Checker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Finder;
use PHPUnit_Framework_TestCase as Phpunit;

class Tester extends Command
{
    const FUNCTIONAL_TEST_IN_DIR      = 'in';
    const FUNCTIONAL_TEST_OUT_DIR     = 'out';
    const FUNCTIONAL_TEST_COMPARE_DIR = 'compare';

    /**
     * @var OutputInterface
     */
    protected $output;

    protected $path;

    protected function configure()
    {
        $this
            ->setName('certi:psr4tester')
            ->setDescription('Runs functional tests!')
            ->addArgument('filter', InputArgument::OPTIONAL, 'Runs only given testcase.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->filter = $input->getArgument('filter');
        $this->output = $output;
        $this->path   = realpath(__DIR__) . '/../../tests/functional/teststore/';

        $dirList = $this->getDirs($this->path, $this->filter);
        if (count($dirList) == 0) {
            // how to throw pseudo-exceptions with this output.object?
            $this->output->writeln('None testcase found for your pattern', OutputInterface::OUTPUT_RAW);
            exit;
        }

        $res = $this->runTests($dirList);
        $output->writeln($res);
    }
    protected function runTests($dirList)
    {
        $fixer = new Fixer();
        $base  = 'Abc\\Def';
        $out   = [];

        foreach ($dirList as $dir)
        {
            $dir .= DIRECTORY_SEPARATOR;

            $inputDirectory   = $dir . self::FUNCTIONAL_TEST_IN_DIR;
            $outputDirectory  = $dir . self::FUNCTIONAL_TEST_OUT_DIR;
            $compareDirectory = $dir . self::FUNCTIONAL_TEST_COMPARE_DIR;

            $out[] = 'DIR: ' . $inputDirectory;

            $this->clearDirectory($outputDirectory);

            $res = $fixer->doit($inputDirectory, $base, $outputDirectory);

            $out[] = $res;

            $this->checkResults($outputDirectory, $compareDirectory);

            #$this->clearDirectory($outputDirectory);
        }

        return implode(PHP_EOL, $out);

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


        // check struct -> number of files etc
        // nimm alle dateien aus den beiden verzeichnisse
        // sortiere nach path.
        // passt die anzahl?
        // passt jeder

        /**
         * @var Finder\SplFileInfo[] $outputFiles
         */
        $outputFiles = [];
        $outputSplFileInfoCollection = $this->getFiles($outputDirectory);
        foreach ($outputSplFileInfoCollection as $oFile) {
            $outputFiles[] = $oFile;
        }

        /**
         * @var Finder\SplFileInfo[] $compareFiles
         */

        $compareFiles = [];
        $compareSplFileInfoCollection = $this->getFiles($compareDirectory);;
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
    protected function getDirs($path, $filter)
    {
        $finder = new Finder\Finder();
        $finder
            ->directories()
            ->in($path)
            ->ignoreDotFiles(true)
            ->depth(0)
        ;

        if (!empty($filter)) {
            $finder->name($filter);
        }

        return $finder;
    }


    /**
     * @param $path
     *
     * @return Finder
     */
    protected function getFiles($path)
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
