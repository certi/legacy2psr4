<?php

namespace Certi\LegacypsrFour\Command;

use Certi\LegacypsrFour\Checker;

use Certi\LegacypsrFour\PhpFile;
use Certi\LegacypsrFour\PhpFileFixer;
use Certi\LegacypsrFour\PhpFileRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Finder;

class Fixer extends Command
{
    protected $checkerList;
    protected $ns;
    protected $path;

    /**
     * @var OutputInterface
     */
    protected $output;

    protected function configure()
    {
        $this
            ->setName('certi:psr4fixer')
            ->setDescription('Converts legacy into psr4 conform!')
            ->addArgument('ns', InputArgument::REQUIRED, 'base namespace. use / instead of \\')
            ->addArgument('path', InputArgument::REQUIRED, 'dirctory containst the legacy code')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->path   = $input->getArgument('path');
        $this->ns     = $input->getArgument('ns');
        $this->output = $output;

        if (!file_exists($this->path)) {
            throw new \InvalidArgumentException('Path does not exists:' . $this->path);
        }

        // normalize ns
        $this->ns = preg_replace('#/#', PhpFile::NS_SEPARATOR, $this->ns);
        if (!preg_match('#' . PhpFile::NS_SEPARATOR . '$#', $this->ns)) {
            $this->ns . PhpFile::NS_SEPARATOR;
        }

        $fileList = $this->getFiles($this->path);

        $output->writeln('Found: ' . count($fileList), 'files.');

        $phpFileRegistry = new PhpFileRegistry();

        /**
         * @var $file SplFileInfo
         */
        foreach ($fileList as $file) {

            $fileHandler = new PhpFile($file, $this->path);
            $fileHandler->check();

            $phpFileRegistry->addFile($fileHandler);

            $output->writeln((string)$fileHandler);
        }

        $output->writeln((string)$phpFileRegistry);

        foreach ($phpFileRegistry->getFileIdList() as $fileID) {

            $fixer = new PhpFileFixer($fileID, $phpFileRegistry);
            $fixer->run();
            $this->output->writeln('FIX file: ' . $fileID . '  (' . $phpFileRegistry->getPhpFileById($fileID)->getAutoloadPath() . ')');
        }

    }

    /**
     * @param $path
     *
     * @return Finder
     */
    protected function getFiles($path)
    {
        $finder = new Finder\Finder();
        $finder->files()
            ->in($path)->name('*.php')
        ;
        return $finder;
    }
}
