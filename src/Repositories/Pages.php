<?php

namespace Nabre\Repositories;

use Nabre\Models\Page;

class Pages
{

    static function isDisabled($name)
    {
        return (bool) (optional(Page::where('name', $name)->first())->disabled ?? false);
    }
    static function isDefinedConfig($name, $part = null)
    {
        return !is_null(self::definedConfig($name, $part));
    }

    static function definedConfig($name, $part = null)
    {
        $value = config('pages.' . self::convertName($name));

        if (!is_null($part)) {
            $value = $value[$part] ?? null;
        }
        return $value;
    }

    static function convertName($name)
    {
        return str_replace(".", "_", $name);
    }

    static function restoreName($name)
    {
        return str_replace("_", ".", $name);
    }
}
