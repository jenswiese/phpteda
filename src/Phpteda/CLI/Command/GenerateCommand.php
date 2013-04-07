<?php

namespace Phpteda\CLI\Command;

use Phpteda\CLI\Config;
use Phpteda\CLI\Helper\Table;
use Phpteda\Generator\Configuration\Configurator;
use Phpteda\Generator\Configuration\Property;
use Phpteda\Generator\Configuration\PropertyGroup;
use Phpteda\Generator\Configuration\PropertySelection;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\DialogHelper;
use Phpteda\Util\GeneratorDirectory;
use InvalidArgumentException;

/**
 * ShowCommand
 *
 * @package Phpteda\CLI\Command
 */
class GenerateCommand extends Command
{
    /** @var Configurator */
    protected $configurator;


    protected function configure()
    {
        $this
            ->setName('generate')
            ->addArgument('generator-file', InputArgument::OPTIONAL, 'Which generator?')
            ->setDescription('Generate test data with given generator file.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws \RuntimeException
     * @throws InvalidArgumentException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dialog = new DialogHelper();
        $config = $this->getApplication()->getConfig();

        if (!$input->getArgument('generator-file')) {
            if ($input->getOption('verbose')) {
                $this->getApplication()->get('show')->execute($input, $output);
            }

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

        if (!$input->getArgument('generator-file')) {
            throw new InvalidArgumentException('No generator-file provided - giving up.');
        }

        $pathname =
            $config->getGeneratorDirectory() .
            DIRECTORY_SEPARATOR .
            $input->getArgument('generator-file') . 'Generator.php';

        $this->configurator = Configurator::createByGeneratorPathname($pathname);

        $this->configureAndRunGenerator($output);

        $output->writeln('Finished generation.');
    }

    /**
     * @param $generatorPathname
     * @param OutputInterface $output
     */
    protected function configureAndRunGenerator(OutputInterface $output)
    {
        $output->writeln('');
        $output->writeln("<info>Configuring generator:</info> " . $this->configurator->getGeneratorClassName());

        $dialog = new DialogHelper();

        foreach ($this->configurator->getProperties() as $property) {
            if ($property instanceof PropertyGroup) {
                $this->configurePropertyGroup($property, $output);
            } elseif ($property instanceof PropertySelection) {
                $this->configurePropertySelection($property, $output);
            }
        }

        $generator = $this->configurator->getConfiguredGenerator();

        $amount = $dialog->ask($output, '<question>How many [1]?</question> ', 1);
        $shouldRemoveExistingData = $dialog->askConfirmation(
            $output,
            '<question>Remove existing data [y/N]?</question> ',
            false
        );

        if ($shouldRemoveExistingData) {
            $generator->shouldRemoveExistingData();
        }

        $generator->amount($amount);
    }


    protected function configurePropertySelection(PropertySelection $selection, OutputInterface $output)
    {
        $dialog = $this->getHelperSet()->get('dialog');
        $selectedKey = $dialog->select($output, $selection->getName(), $selection->getOptions());
        $selection->setSelectedOptionByKey($selectedKey);
    }


    protected function configurePropertyGroup(PropertyGroup $group, OutputInterface $output)
    {
        $dialog = $this->getHelperSet()->get('dialog');
        $output->writeln($group->getName());

        foreach ($group->getProperties() as $property) {
            $question = '<question>' . $property->getQuestion() . ' %s</question> ';
            $defaultValue = false;

            if ($property->isBool()) {
                $question = sprintf($question, '[y/N]?');
                $answer = $dialog->askConfirmation($output, $question, $defaultValue);
            } else {
                $question = sprintf($question, '[]:');
                $answer = $dialog->ask($output, $question, $defaultValue);
            }

            $property->setValue($answer);
        }
    }

}
