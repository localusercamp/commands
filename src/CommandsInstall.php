<?php

namespace TM\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;
use TM\Commands\Makes\MakeModel;

class CommandsInstall extends Command
{
    use InjectorTrait;

    protected $name = 'tm-commands:install';

    protected $description = 'Installs the thread-media commands.';


    protected function getOptions()
    {
        return [
            ['force', null, InputOption::VALUE_NONE, 'Reinstall'],
        ];
    }

    public function handle()
    {
        $is_forse = $this->option('force') ?? false;
        $this->call('vendor:publish', [
            '--provider' => TMCommandsServiceProvider::class,
            '--force'    => $is_forse,
        ]);
        $this->injectMakeModelIntoKernel();
    }

    private function injectMakeModelIntoKernel(): void
    {
        $kernel_file_path = app_path('Console') . DIRECTORY_SEPARATOR . 'Kernel.php';

        $inject       = 'MakeModel::class,';
        $search       = 'protected $commands = [';
        $replace      = "$search\n\t\t$inject\n";
        $file_content = file_get_contents($kernel_file_path);

        $already_has_class = str_contains($file_content, $inject);
        $already_has_use   = str_contains($file_content, 'use ' . MakeModel::class);

        if (!$already_has_class) {
            $injected     = Str::replaceFirst($search, $replace, $file_content);
            file_put_contents($kernel_file_path, $injected);
            $this->info('MakeModel::class was successfully added to the /Console/Kernel.php');
        }
        if (!$already_has_use) {
            $this->injectUseDirective($kernel_file_path, MakeModel::class);
            $this->info('use ' . MakeModel::class . ' was successfully added to the /Console/Kernel.php');
        }
        if ($already_has_class && $already_has_use) {
            $this->warn('/Console/Kernel.php already uses MakeModel::class');
        }
    }
}
