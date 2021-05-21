<?php

namespace TM\Commands\Makes;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

use TM\Commands\StubsTrait;
use TM\Commands\InjectorTrait;
use TM\Commands\ConfigNamespaceImploder;

class MakeEloquentCollection extends GeneratorCommand
{
    use StubsTrait, InjectorTrait;

    protected $name = 'make:collection';

    protected $description = 'Creates a new collection';

    protected $type = 'Collection';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub(): string
    {
        return $this->getEloquenCollectionStubPath();
    }

    protected function getBindStub(): string
    {
        return $this->getEloquenCollectionModelBindStubPath();
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace): string
    {
        return ConfigNamespaceImploder::implode('eloquent-collections', $rootNamespace);
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
            'eloquent-collections',
            trim($this->rootNamespace(), '\\'),
            'CustomEloquentCollection'
        );
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments(): array
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the collection.'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions(): array
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
        $is_new_collection_directive_exists = $this->isNewCollectionDirectiveExistsInFile($model_path);

        if ($is_new_collection_directive_exists) {
            $this->newCollectionExistsWarning($model);
            return false;
        }

        $inject = $this->buildBindStub($name);
        $this->injectIntoEndOfClass($model_path, $inject);

        $inject = $this->qualifyClass($name);
        $this->injectUseDirective($model_path, $inject);

        $this->sortImports($this->files->get($model_path));
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

    protected function newCollectionExistsWarning(string $model_name): void
    {
        $this->warn("The newCollection() method already exists in $model_name model");
    }

    protected function isNewCollectionDirectiveExistsInFile(string $file_path): bool
    {
        return strpos(file_get_contents($file_path), 'function newCollection(') !== false;
    }
}
