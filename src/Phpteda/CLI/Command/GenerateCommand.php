<?php

namespace Phpteda\CLI\Command;

use Phpteda\CLI\Config;
use Phpteda\CLI\Helper\Table;
use Phpteda\Generator\GeneratorBuilder;
use Phpteda\Generator\Configuration\Property;
use Phpteda\Generator\Configuration\PropertyGroup;
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
    /** @var GeneratorBuilder */
    protected $builder;


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
                    'Which generator to take?',
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
        $this->builder = GeneratorBuilder::createByGeneratorPathname($pathname);
        $this->configureAndRunGenerator();

        $this->getIO()->writeInfo('Finished generation.');
    }

    protected function configureAndRunGenerator()
    {
        foreach ($this->builder->getPropertyGroups() as $group) {
            $this->configurePropertyGroup($group);
        }

        $generator = $this->builder->getConfiguredGenerator();

        $amount = $this->getIO()->ask('How many?', 1);
        $shouldRemoveExistingData = $this->getIO()->askConfirmation('Remove existing data?', false);

        if ($shouldRemoveExistingData) {
            $generator->shouldRemoveExistingData();
        }

        $generator->amount($amount);
    }


    protected function configurePropertySelection(PropertySelection $selection)
    {
        $selectedKey = $this->getIO()->choice($selection->getName(), $selection->getOptions());
        if (!is_null($selectedKey)) {
            $selection->setSelectedOptionByKey($selectedKey);
        }
    }

    protected function configurePropertyGroup(PropertyGroup $group)
    {
        $this->getIO()->writeHeader($group->getName());

        foreach ($group->getProperties() as $property) {
            if ($property->isBool()) {
                $answer = $this->getIO()->askConfirmation($property->getQuestion(), false);
            } elseif ($property->hasOptions()) {
                $answer = $this->getIO()->choice($property->getQuestion(), $property->getOptions());
            } else {
                $answer = $this->getIO()->ask($property->getQuestion());
            }

            $property->setValue($answer);
        }
    }

}
