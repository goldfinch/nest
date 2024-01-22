<?php

namespace Goldfinch\Nest\Commands;

use Goldfinch\Taz\Console\GeneratorCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;

#[AsCommand(name: 'make:nest-page')]
class NestPageMakeCommand extends GeneratorCommand
{
    protected static $defaultName = 'make:nest-page';

    protected $description = 'Create a new nest-page class';

    protected $path = 'app/src/Pages/Nest';

    protected $type = 'nest page';

    protected $stub = './stubs/nest-page.stub';

    protected $prefix = '';

    protected function execute($input, $output): int
    {
        parent::execute($input, $output);

        $nameInput = $this->getAttrName($input);

        // Create page controller

        $command = $this->getApplication()->find('make:nest-page-controller');

        $arguments = [
            'name' => $nameInput,
        ];

        $greetInput = new ArrayInput($arguments);
        $returnCode = $command->run($greetInput, $output);

        // Create page template

        $command = $this->getApplication()->find('make:nest-page-template');

        $arguments = [
            'name' => $nameInput,
        ];

        $greetInput = new ArrayInput($arguments);
        $returnCode = $command->run($greetInput, $output);

        return Command::SUCCESS;
    }
}
