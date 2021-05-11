<?php

namespace TM\Commands;

use Illuminate\Support\ServiceProvider;
use TM\Commands\Makes\MakeContract;
use TM\Commands\Makes\MakeAction;
use TM\Commands\Makes\MakeTask;
use TM\Commands\Makes\MakeEntity;
use TM\Commands\Makes\MakeEloquentCollection;
use TM\Commands\Makes\MakeEloquentQueryBuilder;
use TM\Commands\Makes\MakeInit;
use TM\Commands\Makes\MakeModel;

class TMCommandsServiceProvider extends ServiceProvider
{
  /**
   * Register services.
   *
   * @return void
   */
  public function register()
  {
    // $this->mergeConfigFrom(self::CONFIG_PATH, 'tm-commands');
  }

  /**
   * Bootstrap services.
   *
   * @return void
   */
  public function boot()
  {
    $this->publishes([
      __DIR__ . DIRECTORY_SEPARATOR . 'config.php' => config_path('tm-commands.php'),
    ]);

    if ($this->app->runningInConsole()) {
      $this->commands([
        MakeContract::class,
        MakeAction::class,
        MakeTask::class,
        MakeEntity::class,
        MakeEloquentCollection::class,
        MakeEloquentQueryBuilder::class,
        MakeInit::class,
        MakeModel::class,
      ]);
    }
  }
}
