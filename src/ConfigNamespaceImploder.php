<?php

namespace TM\Commands;

use Exception;

class ConfigNamespaceImploder
{
    public static function implode(string $config_key, null|string $prepend = null): string
    {
        $names = config("tm-commands.namespaces.$config_key");
        static::validate($names);
        return ($prepend ? $prepend . '\\' : '') . implode('\\', $names);
    }

    public static function implodeFirstLevel(string $config_key, null|string $prepend = null, null|string $append = null): string
    {
        $names = config("tm-commands.namespaces.$config_key");
        static::validate($names);
        return ($prepend ? $prepend . '\\' : '') . $names[0] . ($append ? '\\' . $append : '');
    }

    private static function validate($names_from_config): void
    {
        if (!is_array($names_from_config) || count($names_from_config) < 1)
            throw new Exception('Your tm-commands config file contains fatal mistakes!');
    }
}
