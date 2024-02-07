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

    protected function execute($input, $output): int
    {
        if (parent::execute($input, $output) === false) {
            return Command::FAILURE;
        }

        $nameInput = $this->getAttrName($input);

        // create page controller
        $command = $this->getApplication()->find('make:nest-page-controller');
        $command->run(new ArrayInput(['name' => $nameInput]), $output);

        // create page template
        $command = $this->getApplication()->find('make:nest-page-template');
        $command->run(new ArrayInput(['name' => $nameInput]), $output);

        return Command::SUCCESS;
    }
}
