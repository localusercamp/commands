<?php

namespace TM\Commands\Makes;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;
use TM\Commands\NamespacesAndPathsTrait;

class MakeInit extends GeneratorCommand
{
  use NamespacesAndPathsTrait;

  protected $name = 'make:init';

  protected $description = 'Generates initial namespaces, folders, classes and contracts.';

  /**
   * Execute the console command.
   */
  public function handle()
  {
    $no_contracts = $this->option('no-contract');
    $no_base      = $this->option('no-base');

    $action_selected     = $this->option('action');
    $task_selected       = $this->option('task');
    $collection_selected = $this->option('collection');
    $builder_selected    = $this->option('builder');

    if (!$action_selected && !$task_selected && !$collection_selected && !$builder_selected) {
      $action_selected     = true;
      $task_selected       = true;
      $collection_selected = true;
      $builder_selected    = true;
    }

    if ($action_selected) {
      if (!$no_base)
        $this->constructBaseAction();
      if (!$no_contracts)
        $this->constructActionContract();
    }

    if ($task_selected) {
      if (!$no_base)
        $this->constructBaseTask();
      if (!$no_contracts)
        $this->constructTaskContract();
    }

    if ($collection_selected) {
      if (!$no_base)
        $this->constructBaseEloquentCollection();
      if (!$no_contracts)
        $this->constructBaseEloquentCollectionContract();
    }

    if ($builder_selected) {
      if (!$no_base)
        $this->constructBaseEloquentQueryBuilder();
      if (!$no_contracts)
        $this->constructBaseEloquentQueryBuilderContract();
    }
  }

  protected function constructBaseClass(string $config_folder, string $file_name, string $stub)
  {
    $path      = $this->getDefaultPathFor($config_folder);
    $file_path = $this->constructPath([$path, $file_name]);
    $is_force  = $this->option('force');

    if (!$this->files->isDirectory($path)) {
      $this->files->makeDirectory($path, 0777, true, true);
    }

    if ($this->files->exists($file_path) && !$is_force) {
      $this->warn("$file_name file already exists, ignoring");
      return false;
    }

    $this->files->put($file_path, $this->sortImports($stub));
  }
  protected function constructBaseAction()
  {
    $stub      = $this->getBaseClassStub('Action');
    $file_name = $this->constructPhpFileName('Action');
    return $this->constructBaseClass('action', $file_name, $stub);
  }
  protected function constructBaseTask()
  {
    $stub      = $this->getBaseClassStub('Task');
    $file_name = $this->constructPhpFileName('Task');
    return $this->constructBaseClass('task', $file_name, $stub);
  }
  protected function constructBaseEloquentCollection()
  {
    $stub      = $this->getBaseClassStub('CustomEloquentCollection');
    $file_name = $this->constructPhpFileName('CustomEloquentCollection');
    return $this->constructBaseClass('eloquent-collection', $file_name, $stub);
  }
  protected function constructBaseEloquentQueryBuilder()
  {
    $stub      = $this->getBaseClassStub('CustomEloquentQueryBuilder');
    $file_name = $this->constructPhpFileName('CustomEloquentQueryBuilder');
    return $this->constructBaseClass('eloquent-query-builder', $file_name, $stub);
  }


  protected function constructClassContract(string $file_name, string $stub)
  {
    $this->constructBaseClass('contract', $file_name, $stub);
  }
  protected function constructActionContract()
  {
    $stub      = $this->getContractStub('IAction');
    $file_name = $this->constructPhpFileName('IAction');
    $this->constructClassContract($file_name, $stub);
  }
  protected function constructTaskContract()
  {
    $stub      = $this->getContractStub('ITask');
    $file_name = $this->constructPhpFileName('ITask');
    $this->constructClassContract($file_name, $stub);
  }
  protected function constructBaseEloquentCollectionContract()
  {
    $stub      = $this->getContractStub('ICustomEloquentCollection');
    $file_name = $this->constructPhpFileName('ICustomEloquentCollection');
    $this->constructClassContract($file_name, $stub);
  }
  protected function constructBaseEloquentQueryBuilderContract()
  {
    $stub      = $this->getContractStub('ICustomEloquentQueryBuilder');
    $file_name = $this->constructPhpFileName('ICustomEloquentQueryBuilder');
    $this->constructClassContract($file_name, $stub);
  }
  
  protected function getBaseClassStub(string $stub_name): string
  {
    return file_get_contents($this->constructPath([__DIR__, 'Stubs', 'Init', 'BaseClasses', "$stub_name.stub"]));
  }

  protected function getContractStub(string $file_name): string
  {
    return file_get_contents($this->constructPath([__DIR__, 'Stubs', 'Init', 'Contracts', "$file_name.stub"]));
  }

  protected function getOptions()
  {
    return [
      ['force',       null, InputOption::VALUE_NONE, 'Run commnad even if the instances already exists'],
      ['no-base',     null, InputOption::VALUE_NONE, 'Generate folder-file structure for base classes'],
      ['no-contract', null, InputOption::VALUE_NONE, 'Generate folder-file structure for contracts'],
      ['action',      'a', InputOption::VALUE_NONE, 'Generate folder-file structure for actions'],
      ['task',        't', InputOption::VALUE_NONE, 'Generate folder-file structure for tasks'],
      ['collection',  'c', InputOption::VALUE_NONE, 'Generate folder-file structure for eloquent collections'],
      ['builder',     'b', InputOption::VALUE_NONE, 'Generate folder-file structure for eloquent query builders'],
    ];
  }

  protected function getArguments()
  {
    return [];
  }
}
