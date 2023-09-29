<?php

namespace Goldfinch\Nest\Commands;

use Goldfinch\Taz\Console\GeneratorCommand;
use Goldfinch\Taz\Services\InputOutput;
use Symfony\Component\Console\Command\Command;

#[AsCommand(name: 'make:nestobject')]
class MakeNestObjectCommand extends GeneratorCommand
{
    protected static $defaultName = 'make:nestobject';

    protected $description = 'Create new Nest Object';

    protected $path = 'app/src/Models/Nested';

    protected $type = 'nestobject';

    protected $stub = './stubs/nestobject.stub';

    protected $prefix = '';

    protected function execute($input, $output): int
    {
        parent::execute($input, $output);

        return Command::SUCCESS;
    }
}
