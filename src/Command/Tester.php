<?php

namespace Certi\LegacypsrFour\Command;

use Certi\LegacypsrFour\Checker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Finder;

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

            $this->clearDirectory($compareDirectory);

            $res = $fixer->doit($inputDirectory, $base, $outputDirectory);

            $out[] = $res;

            $this->checkResults($outputDirectory, $compareDirectory);

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
}
