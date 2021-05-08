<?php

namespace TM\Commands;

trait NamespacesAndPathsTrait
{
  protected string $php_file = '.php';

  protected function getDefaultNamespaceFor(string $name): string
  {
    return 'App' . config("tm-commands.namespaces.$name");
  }

  protected function constructNamespace(array $names): string
  {
    return implode('\\', $names);
  }


  protected function getDefaultPathFor(string $name): string
  {
    return app_path() . DIRECTORY_SEPARATOR . config("tm-commands.path.$name");
  }

  protected function constructPhpFileName(string $file_name): string
  {
    return $file_name . $this->php_file;
  }

  protected function constructPath(array $names): string
  {
    return implode(DIRECTORY_SEPARATOR, $names);
  }

  protected function constructFilePath(array $names, string $ext): string
  {
    return implode(DIRECTORY_SEPARATOR, $names) . $ext;
  }
}
