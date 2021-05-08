<?php

namespace TM\Commands\Makes;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

use TM\Commands\StubsTrait;
use TM\Commands\NamespacesAndPathsTrait;
use TM\Commands\InjectorTrait;

class MakeEloquentCollection extends GeneratorCommand
{
  use StubsTrait, NamespacesAndPathsTrait, InjectorTrait;

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

  /**
   * Get the default namespace for the class.
   *
   * @param  string  $rootNamespace
   * @return string
   */
  protected function getDefaultNamespace($rootNamespace): string
  {
    return $rootNamespace . config('tm-commands.namespaces.eloquent-collections');
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

    $models_namespace = $this->getDefaultNamespaceFor('models');

    $model  = $this->option('model');
    $name   = $this->argument('name');

    if (!$model) return false;

    $model_class     = $this->constructNamespace([$models_namespace, $model]);
    $is_model_exists = class_exists($model_class);

    if (!$is_model_exists) {
      $this->modelNotFoundError($model);
      return false;
    }

    $file_path = $this->constructFilePath([app_path(), 'Models', $model], $this->php_file);
    $is_new_collection_directive_exists = $this->isNewCollectionDirectiveExistsInFile($file_path);

    if ($is_new_collection_directive_exists) {
      $this->newCollectionExistsWarning($model);
      return false;
    }

    $stub_path = $this->getEloquenCollectionModelBindStubPath();
    $inject    = $this->getStubContentAndReplace($stub_path, $name);
    $this->injectIntoEndOfClass($file_path, $inject);

    $namespace = $this->getDefaultNamespaceFor('eloquent-collections');
    $inject    = $this->constructNamespace([$namespace, $name]);
    $this->injectUseDirective($file_path, $inject);
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
