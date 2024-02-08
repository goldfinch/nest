<?php

namespace Goldfinch\Nest\Commands;

use Goldfinch\Taz\Console\GeneratorCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: 'make:nest-object')]
class NestObjectMakeCommand extends GeneratorCommand
{
    protected static $defaultName = 'make:nest-object';

    protected $description = 'Create a new nest-object class';

    protected $path = 'app/src/Models/Nest';

    protected $type = 'nest object';

    protected $stub = './stubs/nest-object.stub';

    protected function configure(): void
    {
        parent::configure();

        $this->addOption(
            'plain',
            null,
            InputOption::VALUE_NONE,
            'Plane model template'
        );

        $this->addOption(
            'fielder',
            null,
            InputOption::VALUE_NONE,
            'Fielder model template'
        );
    }

    protected function execute($input, $output): int
    {
        if ($input->getOption('fielder') !== false) {
            $this->stub = 'nest-object-fielder.stub';
        } else if ($input->getOption('plain') !== false) {
            $this->stub = 'nest-object-plain.stub';
        }

        if (parent::execute($input, $output) === false) {
            return Command::FAILURE;
        }

        $nameInput = $this->getAttrName($input);

        // create nest-object template
        $command = $this->getApplication()->find('make:nest-object-template');
        $command->run(new ArrayInput(['name' => $nameInput]), $output);

        return Command::SUCCESS;
    }
}
