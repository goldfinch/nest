<?php

namespace Goldfinch\Nest\Commands;

use Goldfinch\Taz\Console\GeneratorCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;

#[AsCommand(name: 'make:nest-page-template')]
class NestPageTemplateMakeCommand extends GeneratorCommand
{
    protected static $defaultName = 'make:nest-page-template';

    protected $description = 'Create a new nest-page template';

    protected $path = 'themes/main/templates/App/Pages/Nest/Layout';

    protected $type = 'nest page template';

    protected $stub = 'nest-page-template.stub';

    protected $prefix = '';

    protected $extension = '.ss';

    protected function execute($input, $output): int
    {
        parent::execute($input, $output);

        return Command::SUCCESS;
    }
}
