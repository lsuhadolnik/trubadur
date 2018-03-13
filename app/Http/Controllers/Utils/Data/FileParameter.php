<?php

namespace App\Http\Controllers\Utils\Data;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileParameter extends Parameter
{
    private $value = NULL;
    protected $directory = 'files';

    /**
     * Set the parameter's value.
     *
     * @param  UploadedFile  $value
     * @return void
     **/
    public function setValue(UploadedFile $value)
    {
        $this->value = $value;
    }

    /**
     * Get the parameter's value.
     *
     * @return UploadedFile
     **/
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Storage directory for images.
     **/
    public function getDirectory() {
        return $this->directory();
    }
}
