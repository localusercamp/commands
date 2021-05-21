<?php

namespace TM\Commands;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;
use TM\Commands\ConfigNamespaceImploder;

class ArchInit extends GeneratorCommand
{
    protected $name = 'arch:init';

    protected $description = 'Generates initial namespaces, folders, classes and contracts.';

    protected function getStub()
    {
        return parent::getStub();
    }

    protected function getBaseClassStub(string $stub_name): string
    {
        return $this->files->get($this->constructPath([__DIR__, 'Makes', 'Stubs', 'Init', 'BaseClasses', "$stub_name.stub"]));
    }

    protected function getBaseContractStub(string $stub_name): string
    {
        return $this->files->get($this->constructPath([__DIR__, 'Makes', 'Stubs', 'Init', 'Contracts', "$stub_name.stub"]));
    }

    protected function buildBaseClass(string $name, string $stub_name)
    {
        $stub = $this->getBaseClassStub($stub_name);

        return $this->replaceNamespace($stub, $name)->replaceBaseContractNamespace($stub, $name)->replaceClass($stub, $name);
    }

    protected function buildBaseContract(string $name, string $stub_name)
    {
        $stub = $this->getBaseContractStub($stub_name);

        return $this->replaceNamespace($stub, $name)->replaceClass($stub, $name);
    }

    protected function replaceBaseContractNamespace(&$stub, $name): static
    {
        $searches = [
            ['DummyBaseContractNamespace'],
            ['{{ baseContractNamespace }}'],
            ['{{baseContractNamespace}}'],
        ];

        foreach ($searches as $search) {
            $stub = str_replace(
                $search,
                [$this->getBaseContractNamespace($name)],
                $stub
            );
        }

        return $this;
    }

    protected function getBaseContractNamespace($name): string
    {
        $exploded_namespace = explode('\\', $name);
        $contract_name = 'I' . array_pop($exploded_namespace);
        return ConfigNamespaceImploder::implodeFirstLevel(
            'contracts',
            trim($this->rootNamespace(), '\\'),
            $contract_name,
        );
    }


    protected function constructPath(array $names): string
    {
        return implode(DIRECTORY_SEPARATOR, $names);
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

    protected function constructBaseClass(string $config_key, string $file_name)
    {
        $is_force  = $this->option('force');

        $qualified_name = ConfigNamespaceImploder::implodeFirstLevel(
            $config_key,
            trim($this->rootNamespace(), '\\'),
            $file_name,
        );

        $stub = $this->buildBaseClass($qualified_name, $file_name);
        $path = $this->getPath($qualified_name);

        $this->makeDirectory($path);

        if ($this->files->exists($path) && !$is_force) {
            $this->warn("$file_name file already exists, ignoring");
            return false;
        }
        $this->info("$file_name created successfully");

        $this->files->put($path, $this->sortImports($stub));
    }
    protected function constructBaseAction()
    {
        return $this->constructBaseClass('actions', 'Action');
    }
    protected function constructBaseTask()
    {
        return $this->constructBaseClass('tasks', 'Task');
    }
    protected function constructBaseEloquentCollection()
    {
        return $this->constructBaseClass('eloquent-collections', 'CustomEloquentCollection');
    }
    protected function constructBaseEloquentQueryBuilder()
    {
        return $this->constructBaseClass('eloquent-query-builders', 'CustomEloquentQueryBuilder');
    }


    protected function constructBaseContract(string $file_name)
    {
        $is_force   = $this->option('force');
        $config_key = 'contracts';

        $qualified_name = ConfigNamespaceImploder::implodeFirstLevel(
            $config_key,
            trim($this->rootNamespace(), '\\'),
            $file_name,
        );

        $stub = $this->buildBaseContract($qualified_name, $file_name);
        $path = $this->getPath($qualified_name);

        $this->makeDirectory($path);

        if ($this->files->exists($path) && !$is_force) {
            $this->warn("$file_name file already exists, ignoring");
            return false;
        }
        $this->info("$file_name created successfully");

        $this->files->put($path, $this->sortImports($stub));
    }
    protected function constructActionContract()
    {
        $this->constructBaseContract('IAction');
    }
    protected function constructTaskContract()
    {
        $this->constructBaseContract('ITask');
    }
    protected function constructBaseEloquentCollectionContract()
    {
        $this->constructBaseContract('ICustomEloquentCollection');
    }
    protected function constructBaseEloquentQueryBuilderContract()
    {
        $this->constructBaseContract('ICustomEloquentQueryBuilder');
    }
}
