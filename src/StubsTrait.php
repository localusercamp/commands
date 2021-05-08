<?php

namespace TM\Commands;

trait StubsTrait
{
  protected string $stubs_folder = 'Makes/Stubs';

  protected function getActionStubPath(): string
  {
    return $this->getStubPath('make-action.stub');
  }

  protected function getTaskStubPath(): string
  {
    return $this->getStubPath('make-task.stub');
  }

  protected function getContractStubPath(): string
  {
    return $this->getStubPath('make-contract.stub');
  }

  protected function getEntityStubPath(): string
  {
    return $this->getStubPath('make-entity.stub');
  }

  protected function getEloquenCollectionStubPath(): string
  {
    return $this->getStubPath('make-eloquent-collection.stub');
  }

  protected function getEloquenQueryBuilderStubPath(): string
  {
    return $this->getStubPath('make-eloquent-query-builder.stub');
  }

  protected function getEloquenCollectionModelBindStubPath(): string
  {
    return $this->getStubPath('eloquent-collection-model-bind.stub');
  }

  protected function getEloquenQueryBuilderModelBindStubPath(): string
  {
    return $this->getStubPath('eloquent-query-builder-model-bind.stub');
  }

  protected function getStubPath(string $name): string
  {
    return __DIR__ . DIRECTORY_SEPARATOR . $this->stubs_folder . DIRECTORY_SEPARATOR . $name;
  }

  protected function getStubContentAndReplace(string $stub_path, string $replace): string
  {
    $stub = file_get_contents($stub_path);
    return str_replace(['{{ class }}', '{{class}}'], $replace, $stub);
  }
}
