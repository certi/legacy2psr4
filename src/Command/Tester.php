<?php

namespace Certi\LegacypsrFour\Command;

use Certi\LegacypsrFour\Checker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Tests\Helper\FormatterHelperTest;
use Symfony\Component\Finder;

class Tester extends Command
{
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

        $params = [
            'filter'  => $input->getArgument('filter'),
            'path'    => realpath(__DIR__) . '/../../tests/functional/teststore/',
            'dirList' => $dirList,
        ];

        $testRunner = new TestRunner($params);
        $res        = $testRunner->run();

        $this->prepareResults($res, $this->output);

    }

    /**
     * Shows stats of functional tests
     *
     * @param \PHPUnit_Framework_TestResult $res
     * @param OutputInterface $output
     *
     * @return \PHPUnit_Framework_TestResult
     */
    protected function prepareResults(\PHPUnit_Framework_TestResult $res, OutputInterface $output)
    {
        $runnedTests = $res->count();

        if ($res->failureCount() == 0 && $res->errorCount() == 0) {
            $output->writeln('All ' . $runnedTests . ' tests successfully closed.');
        } else {

            $output->writeln($runnedTests . ' tests at all. ' . $res->failureCount() . ' failures and ' . $res->errorCount() . ' errors.');

            /**
             * @var \PHPUnit_Framework_TestFailure $failure
             */
            foreach ($res->failures() as $failure) {
                $output->writeln('Failed Test name: ' . $failure->getTestName());
                $output->writeln($failure->getExceptionAsString());
            }

            /**
             * @var \PHPUnit_Framework_TestFailure $error
             */
            foreach ($res->errors() as $error) {
                $output->writeln('Error Test name: ' . $error->getTestName());
                $output->writeln($error->getExceptionAsString());
            }

        }

        return $res;

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
