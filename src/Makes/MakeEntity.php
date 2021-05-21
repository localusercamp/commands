<?php

namespace TM\Commands\Makes;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputArgument;
use TM\Commands\ConfigNamespaceImploder;
use TM\Commands\StubsTrait;

class MakeEntity extends GeneratorCommand
{
    use StubsTrait;

    protected $name = 'make:entity';

    protected $description = 'Creates a new entity';

    protected $type = 'Entity';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub(): string
    {
        return $this->getEntityStubPath();
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return ConfigNamespaceImploder::implode('entities', $rootNamespace);
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the entity.'],
        ];
    }
}
