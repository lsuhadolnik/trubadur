<?php

namespace App\Http\Controllers\Utils;

class Helpers
{
    private static $initialized = false;

    /**
     * Static class constructor.
     **/
    private static function initialize()
    {
        if (self::$initialized) {
            return;
        }

        self::$initialized = true;
    }

    /**
     * Check if the given string starts with the specified prefix.
     *
     * @param  string  $input
     * @param  string  $prefix
     * @return boolean
     **/
    public static function startsWith($input, $prefix)
    {
        self::initialize();

        return substr($input, 0, strlen($prefix)) === $prefix;
    }

    /**
     * Check if the given string ends with the specified suffix.
     *
     * @param  string  $input
     * @param  string  $suffix
     * @return boolean
     **/
    public static function endsWith($input, $suffix)
    {
        self::initialize();

        return strlen($suffix) === 0 || substr($input, -strlen($suffix)) === $suffix;
    }

    /**
     * Extract the name of the model.
     *
     * @param  string  $model
     * @return string
     **/
    public static function extractModelName($model)
    {
        self::initialize();

        $tokens = explode('\\', $model);

        return $tokens[count($tokens) - 1];
    }

    /**
     * Remove dependency key id suffix.
     *
     * @param  string  $key
     * @return string
     **/
    public static function removeIdSuffix($key)
    {
        self::initialize();

        return substr($key, 0, strlen($key) - 3);
    }
}
