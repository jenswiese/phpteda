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
        if (!$this->getIO()->getArgument('generator-file')) {
            if ($input->getOption('verbose')) {
                $this->getApplication()->get('show')->execute($input, $output);
            }

            $generatorDirectory = new GeneratorDirectory($this->getConfig()->getGeneratorDirectory());

            $this->getIO()->setArgument(
                'generator-file',
                $this->getIO()->askWithSuggestions(
                    '<question>Which generator to take?</question> ',
                    $generatorDirectory->getGeneratorNames()
                )
            );
        }

        if (!$this->getIO()->getArgument('generator-file')) {
            throw new InvalidArgumentException('No generator-file provided - giving up.');
        }

        $pathname = sprintf(
            '%s' . DIRECTORY_SEPARATOR . '%sGenerator.php',
            $this->getConfig()->getGeneratorDirectory(),
            $input->getArgument('generator-file')
        );
        $this->configurator = Configurator::createByGeneratorPathname($pathname);
        $this->configureAndRunGenerator();

        $this->getIO()->write('Finished generation.');
    }

    protected function configureAndRunGenerator()
    {
        foreach ($this->configurator->getProperties() as $property) {
            if ($property instanceof PropertyGroup) {
                $this->configurePropertyGroup($property);
            } elseif ($property instanceof PropertySelection) {
                $this->configurePropertySelection($property);
            }
        }

        $generator = $this->configurator->getConfiguredGenerator();

        $amount = $this->getIO()->ask('<question>How many [1]?</question> ', 1);
        $shouldRemoveExistingData = $this->getIO()->askConfirmation(
            '<question>Remove existing data [y/N]?</question> ',
            false
        );

        if ($shouldRemoveExistingData) {
            $generator->shouldRemoveExistingData();
        }

        $generator->amount($amount);
    }


    protected function configurePropertySelection(PropertySelection $selection)
    {
        $this->getIO()->write('');

        $question = sprintf('<question>%s:</question>', $selection->getName());
        $selectedKey = $this->getIO()->choice($question, $selection->getOptions());
        if (!is_null($selectedKey)) {
            $selection->setSelectedOptionByKey($selectedKey);
        }
    }

    protected function configurePropertyGroup(PropertyGroup $group)
    {
        $this->getIO()->write('');
        $this->getIO()->write('<question>' . $group->getName() . ':</question>');

        foreach ($group->getProperties() as $property) {
            $question = $property->getQuestion() . ' %s ';
            $defaultValue = false;

            if ($property->isBool()) {
                $question = sprintf($question, ' [y/N]?');
                $answer = $this->getIO()->askConfirmation($question, $defaultValue);
            } else {
                $question = sprintf($question, ' []:');
                $answer = $this->getIO()->ask($question, $defaultValue);
            }

            $property->setValue($answer);
        }
    }

}
