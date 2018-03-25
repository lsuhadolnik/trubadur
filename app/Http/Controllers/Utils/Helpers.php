<?php

namespace App\Http\Controllers\Utils;

class Helpers
{
    private static $initialized = false;

    private static $FILTER_ID_INDICATOR = 'id';

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
     * Check if the given string contains a substring.
     *
     * @param  string  $input
     * @param  string  $query
     * @return boolean
     **/
    public static function contains($input, $query)
    {
        self::initialize();

        $index = strpos($input, $query);

        return is_int($index) && $index >= 0;
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
     * Create prettified printable version of a composite key.
     *
     * @param  array  $compositeKey
     * @return string
     **/
    public static function getPrintableCompositeKey($compositeKey)
    {
        self::initialize();

        $keys = array_keys($compositeKey);
        $ids = array_values($compositeKey);

        $value = '(';
        $value .= $keys[0] . ' => ' . $ids[0] . ', ';
        $value .= $keys[1] . ' => ' . $ids[1];
        $value .= ')';

        return $value;
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

        if (self::endsWith($key, self::$FILTER_ID_INDICATOR)) {
            return substr($key, 0, strlen($key) - 3);
        }

        return $key;
    }
}
