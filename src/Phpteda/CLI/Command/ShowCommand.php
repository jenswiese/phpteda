<?php

namespace Phpteda\CLI\Command;

use Phpteda\CLI\Helper\Table;
use Phpteda\Reflection\ReflectionClass;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\DialogHelper;
use Phpteda\Util\GeneratorDirectoryIterator;
use DirectoryIterator;

/**
 * ShowCommand
 *
 * @package Phpteda\CLI\Command
 */
class ShowCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('show')
            ->setDescription('Show Generators and their settings');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws \RuntimeException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getIO()->write($this->getApplication()->getLongVersion());
        $this->getIO()->write('');

        if (!$this->getConfig()->hasGeneratorDirectory()) {
            throw new \RuntimeException("Generator directory is not set. Please run 'init' command first.");
        }

        $this->getIO()->write('<comment>Using:</comment> ' . $this->getConfig()->getGeneratorDirectory());

        $table = Table::create($this->getIO(), $this->getApplication()->getTerminalWidth())
            ->addRow()
                ->addColumn('<comment>Generator</comment>')
                ->addColumn('<comment>Description</comment>');

        foreach ($this->getDirectoryIterator() as $generatorFile) {
            $description = ReflectionClass::createByPathname($generatorFile->getPathname())
                ->getAnnotationReader()->getDescription();

            $table->addRow()
                ->addColumn($generatorFile->getBasename('Generator.php'))
                ->addColumn($description);
        }

        $table->end();
    }

    /**
     * @return GeneratorDirectoryIterator
     */
    protected function getDirectoryIterator()
    {
        return new GeneratorDirectoryIterator(
            new DirectoryIterator($this->getConfig()->getGeneratorDirectory())
        );
    }
}
