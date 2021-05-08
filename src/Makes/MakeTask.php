<?php

namespace TM\Commands\Makes;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputArgument;

use TM\Commands\StubsTrait;

class MakeTask extends GeneratorCommand
{
  use StubsTrait;

  protected $name = 'make:task';

  protected $description = 'Creates a new task';

  protected $type = 'Task';

  /**
   * Get the stub file for the generator.
   *
   * @return string
   */
  protected function getStub(): string
  {
    return $this->getTaskStubPath();
  }

  /**
   * Get the default namespace for the class.
   *
   * @param  string  $rootNamespace
   * @return string
   */
  protected function getDefaultNamespace($rootNamespace)
  {
    return $rootNamespace . config('tm-commands.namespaces.tasks');
  }

  /**
   * Get the console command arguments.
   *
   * @return array
   */
  protected function getArguments()
  {
    return [
      ['name', InputArgument::REQUIRED, 'The name of the task.'],
    ];
  }
}
