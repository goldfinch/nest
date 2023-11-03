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

    protected $stub = 'nest-object.stub';

    protected $prefix = '';

    protected function execute($input, $output): int
    {
        parent::execute($input, $output);

        $nameInput = $this->getAttrName($input);

        // Create nest-object template

        $command = $this->getApplication()->find('make:nest-object-template');

        $arguments = [
            'name' => $nameInput,
        ];

        $greetInput = new ArrayInput($arguments);
        $returnCode = $command->run($greetInput, $output);

        return Command::SUCCESS;
    }
}
