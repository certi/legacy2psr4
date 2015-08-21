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
    protected function configure()
    {
        $this
            ->setName('certi:psr4fixer')
            ->setDescription('Converts legacy into psr4 conform!')
            ->addArgument('ns', InputArgument::REQUIRED, 'base namespace. use / instead of \\')
            ->addArgument('path', InputArgument::REQUIRED, 'directory containst the legacy code')
            ->addArgument('target', InputArgument::OPTIONAL, 'target directory. Need if you want save')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path      = $input->getArgument('path');
        $namespace = $input->getArgument('ns');
        $target    = $input->getArgument('target');

        if (!file_exists($path)) {
            throw new \InvalidArgumentException('Path does not exists:' . $path);
        }

        // normalize ns
        $namespace = preg_replace('#/#', PhpFile::NS_SEPARATOR, $namespace);
        if (!preg_match('#' . PhpFile::NS_SEPARATOR . '$#', $namespace)) {
            $namespace .= PhpFile::NS_SEPARATOR;
        }

        $res = $this->doit((string)$path, $namespace, $target);

        $output->writeln($res);
    }

    /**
     * Fixes
     *
     * @todo: Move it into own class
     *
     * @param $path
     * @param $namespace
     * @return string
     */
    public function doit($path, $namespace, $target)
    {

        $fileList = $this->getFiles($path);

        $output   = [];
        $output[] = 'Found: ' . count($fileList) . 'files.';

        $phpFileRegistry = new PhpFileRegistry();

        /**
         * @var $file SplFileInfo
         */
        foreach ($fileList as $file) {

            $fileHandler = new PhpFile($file, $path);
            $fileHandler->check();

            $phpFileRegistry->addFile($fileHandler);

            $output[] = (string)$fileHandler;
        }

        $output[] = (string)$phpFileRegistry;

        foreach ($phpFileRegistry->getFileIdList() as $fileID) {

            $fixer = new PhpFileFixer($fileID, $phpFileRegistry);
            $fixer->run();
            if (!empty($target)) {
                // save the changes only if target defined
                $fixer->persist($target);
            }

            $txt   = [];
            $txt[] = 'FIX file: ';
            $txt[] = str_pad($fileID, 9, ' ');
            $txt[] = $phpFileRegistry->getPhpFileById($fileID)->getAutoloadPath();

            $output[] = implode(' ', $txt);
        }

        return implode(PHP_EOL, $output);
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
            ->name('*.php');
        return $finder;
    }

}
