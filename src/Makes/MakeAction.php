<?php

namespace TM\Commands\Makes;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputArgument;

use TM\Commands\StubsTrait;

class MakeAction extends GeneratorCommand
{
  use StubsTrait;

  protected $name = 'make:action';

  protected $description = 'Creates a new action';

  protected $type = 'Action';

  /**
   * Get the stub file for the generator.
   *
   * @return string
   */
  protected function getStub(): string
  {
    return $this->getActionStubPath();
  }

  /**
   * Get the default namespace for the class.
   *
   * @param  string  $rootNamespace
   * @return string
   */
  protected function getDefaultNamespace($rootNamespace)
  {
    return $rootNamespace . config('tm-commands.namespaces.actions');
  }

  /**
   * Get the console command arguments.
   *
   * @return array
   */
  protected function getArguments()
  {
    return [
      ['name', InputArgument::REQUIRED, 'The name of the action.'],
    ];
  }
}
