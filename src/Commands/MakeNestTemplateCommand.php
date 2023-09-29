<?php

namespace Goldfinch\Nest\Commands;

use Goldfinch\Taz\Console\GeneratorCommand;
use Goldfinch\Taz\Services\InputOutput;
use Symfony\Component\Console\Command\Command;

#[AsCommand(name: 'make:nesttemplate')]
class MakeNestTemplateCommand extends GeneratorCommand
{
    protected static $defaultName = 'make:nesttemplate';

    protected $description = 'Create new Nest template';

    protected $path = 'themes/main/templates/App/Models';

    protected $type = 'nesttemplate';

    protected $stub = './stubs/nestobject.stub';

    protected $prefix = '';

    protected function execute($input, $output): int
    {
        parent::execute($input, $output);

        return Command::SUCCESS;
    }
}
