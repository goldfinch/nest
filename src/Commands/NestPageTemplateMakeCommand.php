<?php

namespace Goldfinch\Nest\Commands;

use Goldfinch\Taz\Console\GeneratorCommand;

#[AsCommand(name: 'make:nest-page-template')]
class NestPageTemplateMakeCommand extends GeneratorCommand
{
    protected static $defaultName = 'make:nest-page-template';

    protected $description = 'Create a new nest-page template';

    protected $path = 'themes/[theme]/templates/[namespace_root]';

    protected $type = 'nest page template';

    protected $stub = './stubs/nest-page-template.stub';

    protected $extension = '.ss';
}
