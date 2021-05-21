<?php

namespace TM\Commands\Makes;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputArgument;
use TM\Commands\ConfigNamespaceImploder;
use TM\Commands\StubsTrait;

class MakeContract extends GeneratorCommand
{
    use StubsTrait;

    protected $name = 'make:contract';

    protected $description = 'Creates a new contract';

    protected $type = 'Contract';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub(): string
    {
        return $this->getContractStubPath();
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return ConfigNamespaceImploder::implode('contracts', $rootNamespace);
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the contract.'],
        ];
    }
}
