<?php

namespace App\Http\Controllers\Utils;

class File
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
     * Save the file (image / audio) to the storage.
     *
     * @param  string  $directory
     * @return array
     **/
    public static function save($file, $directory)
    {
        self::initialize();

        if ($file->isValid()) {
            $extension = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
            $path = $file->storeAs($directory, uniqid() . '.' . $extension, 'public');
            if (!$path) {
                return ['success' => false, 'data' => "Saving of the file {$file->getClientOriginalName()} failed."];
            }
            return ['success' => true, 'data' => '/storage/' . $path];
        } else {
            return ['success' => false, 'data' => "File {$file->getClientOriginalName()} is not valid."];
        }
    }
}
