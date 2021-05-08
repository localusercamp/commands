<?php

namespace TM\Commands\Makes;

use Illuminate\Foundation\Console\ModelMakeCommand;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class MakeModel extends ModelMakeCommand
{
  /**
   * The console command name.
   *
   * @var string
   */
  protected $name = 'make:model';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Create a new Eloquent model class';

  /**
   * The type of class being generated.
   *
   * @var string
   */
  protected $type = 'Model';

  public function handle()
  {
    if (parent::handle() === false) return false;

    if ($this->option('collection')) {
      $this->createCollection();
    }

    if ($this->option('query-builder')) {
      $this->createQueryBuilder();
    }
  }

  /**
   * Get the console command options.
   *
   * @return array
   */
  protected function getOptions(): array
  {
    $options = parent::getOptions();
    $options[] = ['collection', null, InputOption::VALUE_NONE, 'Create a new eloquent collection for the model'];
    $options[] = ['query-builder', null, InputOption::VALUE_NONE, 'Create a new eloquent query builder for the model'];
    return $options;
  }

  protected function createCollection(): void
  {
    $model = Str::studly($this->argument('name'));

    $this->call('make:collection', [
      'name' => "{$model}Collection",
      '--model' => $model,
    ]);
  }

  protected function createQueryBuilder(): void
  {
    $model = Str::studly($this->argument('name'));

    $this->call('make:query-builder', [
      'name' => "{$model}QueryBuilder",
      '--model' => $model,
    ]);
  }
}
