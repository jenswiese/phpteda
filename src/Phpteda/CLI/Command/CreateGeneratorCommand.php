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
class CreateGeneratorCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('create-generator')
            ->setDescription('Create new Generator');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws \RuntimeException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->getConfig()->hasGeneratorDirectory()) {
            throw new \RuntimeException("Generator directory is not set. Please run 'init' command first.");
        }

        $this->getIO()->write('<comment>Using:</comment> ' . $this->getConfig()->getGeneratorDirectory());

        $name = $this->getIO()->ask('Name of Generator');
        $description = $this->getIO()->ask('Description');
        $namespace = $this->getIO()->ask('Namespace', $this->getConfig()->getGeneratorNamespace());
        $this->getConfig()->setGeneratorNamespace($namespace);

        $filepath = $this->getConfig()->getGeneratorDirectory() . DIRECTORY_SEPARATOR . $name .  'Generator.php';
        if (file_exists($filepath)) {
            $overwriteExistingFile = $this->getIO()->askConfirmation('File already exists, overwrite it?', false);
            if (!$overwriteExistingFile) {
                $this->getIO()->writeInfo('Skipped generation, nothing happened.');
                return;
            }
        }

        file_put_contents($filepath, $this->getGeneratorCode($name, $description, $namespace));

        $this->getIO()->writeInfo('Generated file "' . $filepath . '".');
    }

    /**
     * Returns Generator code
     *
     * @param $name
     * @param $description
     * @return string
     */
    public function getGeneratorCode($name, $description, $namespace)
    {
        $code = <<< EOT
<?php

namespace %s;

use Faker\Factory;

/**
 * %s
 *
 * @author <YOUR NAME HERE>
 * @since %s
 *
 */
class %sGenerator extends \Phpteda\Generator\AbstractGenerator
{
    /**
     * Implements custom way to delete existing data
     *
     * @return self
     */
    protected function removeExistingData()
    {
        // @todo: implement removing of your data
    }

    /**
     * Implements custom behaviour prior generation (e.g. for XML-head)
     *
     * @return self
     */
    protected function preGeneration()
    {
        // @todo: implement or remove method if not needed
    }

    /**
     * Implements custom behaviour after generation (e.g. for XML-head)
     *
     * @return self
     */
    protected function postGeneration()
    {
        // @todo: implement or remove method if not needed
    }

    /**
     * Implements custom generator behaviour
     *
     * @return void
     */
    protected function generateData()
    {
        // @todo: implement generating of single item
    }
}
EOT;

        return sprintf(
            $code,
            $namespace,
            $description,
            date('Y-m-d'),
            $name
        );
    }
}
