<?php

namespace Goldfinch\Nest\Commands;

use Goldfinch\Taz\Console\GeneratorCommand;

#[AsCommand(name: 'make:nest-page-controller')]
class MakeNestPageControllerCommand extends GeneratorCommand
{
    protected static $defaultName = 'make:nest-page-controller';

    protected $description = 'Create a new nest-page controller class';

    protected $path = 'app/src/Controllers/Nest';

    protected $type = 'nest page controller';

    protected $stub = './stubs/nest-page-controller.stub';

    protected $prefix = 'Controller';
}
