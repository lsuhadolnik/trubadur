<?php

namespace App\Http\Controllers\Utils\Data;

class Parameter
{
    private $key = '';
    private $dependency = false;
    private $pivotDependency = false;

    /**
     * Set the parameter's key.
     *
     * @param  string  $key
     * @return void
     **/
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * Get the parameter's key.
     *
     * @return string
     **/
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Define parameter as a dependency.
     *
     * @param  boolean $input
     * @return void
     **/
    public function setDependency($input)
    {
        $this->dependency = $input;
    }

    /**
     * Check if the parameter defines a dependency.
     *
     * @return boolean
     **/
    public function isDependency()
    {
        return $this->dependency;
    }

    /**
     * Define parameter as a pivot dependency.
     *
     * @param  boolean $input
     * @return void
     **/
    public function setPivotDependency($input)
    {
        $this->pivotDependency = $input;
    }

    /**
     * Check if the parameter defines a pivot dependency.
     *
     * @return boolean
     **/
    public function isPivotDependency()
    {
        return $this->pivotDependency;
    }
}
