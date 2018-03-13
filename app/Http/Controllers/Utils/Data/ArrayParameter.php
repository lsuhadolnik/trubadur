<?php

namespace App\Http\Controllers\Utils\Data;

class ArrayParameter extends Parameter
{
    private $value = [];

    /**
     * Set the parameter's value.
     *
     * @param  array  $value
     * @return void
     **/
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * Get the parameter's value.
     *
     * @return array
     **/
    public function getValue()
    {
        return $this->value;
    }
}
