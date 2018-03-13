<?php

namespace App\Http\Controllers\Utils\Data;

class NumericParameter extends Parameter
{
    private $value = 0;

    /**
     * Set the parameter's value.
     *
     * @param  integer|numeric  $value
     * @return void
     **/
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * Get the parameter's value.
     *
     * @return integer|numeric
     **/
    public function getValue()
    {
        return $this->value;
    }
}
