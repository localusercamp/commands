<?php

namespace TM\Commands\Makes;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use TM\Commands\ConfigNamespaceImploder;
use TM\Commands\InjectorTrait;
use TM\Commands\StubsTrait;

class MakeEloquentQueryBuilder extends GeneratorCommand
{
    use StubsTrait, InjectorTrait;

    protected $name = 'make:query-builder';

    protected $description = 'Creates a new query builder';

    protected $type = 'QueryBuilder';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub(): string
    {
        return $this->getEloquenQueryBuilderStubPath();
    }

    protected function getBindStub(): string
    {
        return $this->getEloquenQueryBuilderModelBindStubPath();
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return ConfigNamespaceImploder::implode('eloquent-query-builders', $rootNamespace);
    }

    protected function replaceNamespace(&$stub, $name)
    {
        $searches = [
            ['DummyNamespace', 'DummyRootNamespace', 'NamespacedDummyUserModel', 'BaseClassNamespace'],
            ['{{ namespace }}', '{{ rootNamespace }}', '{{ namespacedUserModel }}', '{{ baseClassNamespace }}'],
            ['{{namespace}}', '{{rootNamespace}}', '{{namespacedUserModel}}', '{{baseClassNamespace}}'],
        ];

        foreach ($searches as $search) {
            $stub = str_replace(
                $search,
                [$this->getNamespace($name), $this->rootNamespace(), $this->userProviderModel(), $this->getBaseClassNamespace()],
                $stub
            );
        }

        return $this;
    }

    protected function getBaseClassNamespace(): string
    {
        return ConfigNamespaceImploder::implodeFirstLevel(
            'eloquent-query-builders',
            trim($this->rootNamespace(), '\\'),
            'CustomEloquentQueryBuilder'
        );
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the query builder.'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            new InputOption('model', 'm', InputOption::VALUE_REQUIRED, 'The name of the model to bind to'),
        ];
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (parent::handle() === false) return false;

        $model  = $this->option('model');
        $name   = $this->argument('name');

        if (!$model) return false;

        $qualified_model = $this->qualifyModel($model);
        $is_model_exists = $this->alreadyExists($qualified_model);

        if (!$is_model_exists) {
            $this->modelNotFoundError($model);
            return false;
        }

        $model_path = $this->getPath($qualified_model);
        $is_new_eloquent_builder_directive_exists = $this->isNewEloquentBuilderDirectiveExistsInFile($model_path);

        if ($is_new_eloquent_builder_directive_exists) {
            $this->newEloquentBuilderExistsWarning($model);
            return false;
        }

        $inject = $this->buildBindStub($name);
        $this->injectIntoEndOfClass($model_path, $inject);

        $inject = $this->qualifyClass($name);
        $this->injectUseDirective($model_path, $inject);
    }



    protected function buildBindStub($name)
    {
        $stub = $this->files->get($this->getBindStub());
        return $this->replaceNamespace($stub, $name)->replaceClass($stub, $name);
    }

    protected function modelNotFoundError(string $model_name): void
    {
        $this->error("$model_name model not found!");
    }

    protected function newEloquentBuilderExistsWarning(string $model_name): void
    {
        $this->warn("The newEloquentBuilder() method already exists in $model_name model");
    }

    protected function isNewEloquentBuilderDirectiveExistsInFile(string $file_path): bool
    {
        return str_contains(file_get_contents($file_path), 'function newEloquentBuilder(');
    }
}
