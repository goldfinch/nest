<?php

namespace Goldfinch\Nest\Commands;

use Goldfinch\Taz\Console\GeneratorCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;

#[AsCommand(name: 'make:nest-object')]
class NestObjectMakeCommand extends GeneratorCommand
{
    protected static $defaultName = 'make:nest-object';

    protected $description = 'Create a new nest-object class';

    protected $path = 'app/src/Models/Nest';

    protected $type = 'nest object';

    protected $stub = './stubs/nest-object.stub';

    protected function execute($input, $output): int
    {
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
