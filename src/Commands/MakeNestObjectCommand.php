<?php

namespace Goldfinch\Nest\Commands;

use Goldfinch\Taz\Console\GeneratorCommand;
use Goldfinch\Taz\Services\InputOutput;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;

#[AsCommand(name: 'make:nestobject')]
class MakeNestObjectCommand extends GeneratorCommand
{
    protected static $defaultName = 'make:nestobject';

    protected $description = 'Create new Nest Object';

    protected $path = 'app/src/Models';

    protected $type = 'nestobject';

    protected $stub = './stubs/nestobject.stub';

    protected $prefix = '';

    protected function execute($input, $output): int
    {
        parent::execute($input, $output);

        $nameInput = $this->getAttrName($input);

        // Nest template

        $command = $this->getApplication()->find('make:nest-template');

        $arguments = [
            'name'    => $nameInput,
        ];

        $greetInput = new ArrayInput($arguments);
        $returnCode = $command->run($greetInput, $output);

        return Command::SUCCESS;
    }
}
