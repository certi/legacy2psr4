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
        $this->path   = realpath(__DIR__) . '../tests/functional/teststore/';

        $dirList = $this->getDirs($this->path, $this->filter);
        if (count($dirList) == 0) {
            // how to throw pseudo-exceptions with this output.object?
            $this->output->writeln('None testcase found for your pattern', OutputInterface::OUTPUT_RAW);
            exit;
        }

        $this->runTests($dirList);

    }

    protected function runTests($dirList)
    {

        foreach ($dirList as $dir) {

        }

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
            ->in($path);

        if (!empty($filter)) {
            $finder->name($filter);
        }

        return $finder;
    }
}
