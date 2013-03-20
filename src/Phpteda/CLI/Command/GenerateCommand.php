<?php

namespace Phpteda\CLI\Command;

use Phpteda\CLI\Helper\Table;
use Phpteda\Generator\Configuration\Configurator;
use Phpteda\Generator\GeneratorInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\DialogHelper;
use Phpteda\Util\GeneratorDirectory;

/**
 * ShowCommand
 *
 * @package Phpteda\CLI\Command
 */
class GenerateCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('generate')
            ->addArgument('generator-file', InputArgument::OPTIONAL, 'Which generator?')
            ->setDescription('Generate test data with given generator files.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws \RuntimeException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$input->hasArgument('generate-file')) {
            $this->getApplication()->get('show')->execute($input, $output);

            $dialog = new DialogHelper();
            $config = $this->getApplication()->getConfig();
            $generatorDirectory = new GeneratorDirectory($config->getGeneratorDirectory());

            $input->setArgument(
                'generator-file',
                $dialog->ask(
                    $output,
                    '<question>Which generator to take?</question> ',
                    null,
                    $generatorDirectory->getGeneratorNames()
                )
            );
        }

        $this->configureGenerator($input->getArgument('generator-file'), $output);
     }

    protected function configureGenerator($generatorPathname, OutputInterface $output)
    {
        $dialog = new DialogHelper();
        $configurator = new Configurator($generatorPathname);

        foreach ($configurator->getProperties() as $property) {
            $question = '<question>' . $property->getQuestion() . '</question> ';
            $defaultValue = false;

            if ($property->isBool()) {
                $answer = $dialog->askConfirmation($output, $question, $defaultValue);
            } else {
                $answer = $dialog->ask($output, $question, $defaultValue);
            }

            $property->setValue($answer);
        }

        $generator = $configurator->getConfiguredGenerator();
        $generator->amount(1);
    }
}
