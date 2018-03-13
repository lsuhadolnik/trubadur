<?php

namespace App\Http\Controllers\Utils\Data;

class StringParameter extends Parameter
{
    private $value = '';
    private $password = false;

    /**
     * Set the parameter's value.
     *
     * @param  string  $value
     * @return void
     **/
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * Get the parameter's value.
     *
     * @return string
     **/
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Define parameter as a password.
     *
     * @param  boolean $input
     * @return void
     **/
    public function setPassword($input)
    {
        $this->password = $input;
    }

    /**
     * Check if the parameter defines a password.
     *
     * @return boolean
     **/
    public function isPassword()
    {
        return $this->password;
    }
}
