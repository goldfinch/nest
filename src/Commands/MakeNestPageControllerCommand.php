<?php

namespace Goldfinch\Nest\Commands;

use Goldfinch\Taz\Console\GeneratorCommand;
use Symfony\Component\Console\Command\Command;

#[AsCommand(name: 'make:nest-page-controller')]
class MakeNestPageControllerCommand extends GeneratorCommand
{
    protected static $defaultName = 'make:nest-page-controller';

    protected $description = 'Create a new nest-page controller class';

    protected $path = 'app/src/Controllers/Nest';

    protected $type = 'nest page controller';

    protected $stub = 'nest-page-controller.stub';

    protected $prefix = 'Controller';

    protected function execute($input, $output): int
    {
        parent::execute($input, $output);

        return Command::SUCCESS;
    }
}
