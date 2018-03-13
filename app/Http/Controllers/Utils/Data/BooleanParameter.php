<?php

namespace App\Http\Controllers\Utils\Data;

class BooleanParameter extends Parameter
{
    private $value = false;

    /**
     * Set the parameter's value.
     *
     * @param  boolean
     * @return void
     **/
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * Get the parameter's value.
     *
     * @return boolean
     **/
    public function getValue()
    {
        return $this->value;
    }
}
