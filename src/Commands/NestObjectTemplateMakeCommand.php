<?php

namespace Goldfinch\Nest\Commands;

use Goldfinch\Taz\Console\GeneratorCommand;
use Symfony\Component\Console\Command\Command;

#[AsCommand(name: 'make:nest-object-template')]
class NestObjectTemplateMakeCommand extends GeneratorCommand
{
    protected static $defaultName = 'make:nest-object-template';

    protected $description = 'Create a new nest-object template';

    protected $path = 'themes/[theme]/templates/[namespace_root]';

    protected $type = 'nest page template';

    protected $stub = './stubs/nest-object-template.stub';

    protected $prefix = '';

    protected $extension = '.ss';

    protected function execute($input, $output): int
    {
        parent::execute($input, $output);

        return Command::SUCCESS;
    }
}
