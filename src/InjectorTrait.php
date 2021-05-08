<?php

namespace TM\Commands;

use Illuminate\Support\Str;

trait InjectorTrait
{
  public function injectIntoEndOfClass(string $path_to_file, string $inject): void
  {
    $replace      = "{$inject}\n}";
    $file_content = file_get_contents($path_to_file);
    $injected     = Str::replaceLast('}', $replace, $file_content);
    file_put_contents($path_to_file, $injected);
  }

  public function injectUseDirective(string $path_to_file, string $inject): void
  {
    $file_content = file_get_contents($path_to_file);
    $between      = Str::before(Str::after($file_content, 'namespace'), ";");
    $search       = "namespace{$between};";
    $replace      = "{$search}\n\nuse {$inject};";
    $injected     = Str::replaceLast($search, $replace, $file_content);
    file_put_contents($path_to_file, $injected);
  }
}
