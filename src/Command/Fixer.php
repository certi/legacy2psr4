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
        $this->ns = preg_replace('#/#', '\\', $this->ns);
        if (!preg_match('#\\$#', $this->ns)) {
            $this->ns . '\\';
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
            $this->output->writeln('FIX file: ' . $fileID);
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

__halt_compiler();

/*
protected function handleFile(Finder\SplFileInfo $file)
{

    $base     = realpath($this->path);
    $realpath = $file->getRealPath();
    $autoload_path = $this->getAutoloadPath($base, $realpath);

    $expected_ns = $this->convertPathToNs($autoload_path);

    $content = $file->getContents();


    $checkList   = [];
    $checkList[] = new Checker\GetClassName($this);
    $checkList[] = new Checker\HasNamespace($this);
    $checkList[] = new Checker\HasUseNamespace($this);

    // pass namespace to path?
    // pass class to filename?
    // has multiple namespaces?

    /**
     * @var $check CheckerInterface
     * /
    foreach ($checkList as $check) {
        $check->execute($content);
    }

    #// uses anyher classes/interfaces/traits
    #$this->checkRelatedClasses($content);
    #$this->output->writeln($autoload_path . '=>' . $expected_ns);
}


protected function checkRelatedClasses($content)
{
    $lex   = [];
    $lex[] = new Lexer\Instantiate($this);
    $lex[] = new Lexer\StaticCall($this);
    $lex[] = new Lexer\TypeHinting($this);

    /**
     * @var $lexItem LexerInterface
     * /
    foreach ($lex as $lexItem) {
        $lexItem->execute($content);
    }

}

protected function getAutoloadPath($base, $realpath)
{
    return substr($realpath, strlen($base));
}

/**
 * @todo: Windows? tfuj
 *
 * @param $path
 * /
protected function convertPathToNs($path)
{
    $first = '';
    $file  = '';
    if (preg_match('#(.*)\/([^\/]*)#', $path, $matches)) {
        $first = $matches[1];
        $file  = $matches[2];
    } elseif (preg_match('#/(.*)#', $path, $matches)) {
        $file  = $matches[1];
    } else {
        throw new Exception('Uknown format: ' . $path);
    }

    $ns  = $this->ns . preg_replace('#/#', '\\', $first);

    return $ns;
}
*/
