<?php

namespace Goldfinch\Nest\Commands;

use Goldfinch\Taz\Console\GeneratorCommand;
use Symfony\Component\Console\Command\Command;

#[AsCommand(name: 'make:nest-model')]
class NestObjectMakeCommand extends GeneratorCommand
{
    protected static $defaultName = 'make:nest-model';

    protected $description = 'Create a new nest-model class';

    protected $path = 'app/src/Models/Nest';

    protected $type = 'nest model';

    protected $stub = 'nest-model.stub';

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
