<?php

namespace App\Http\Controllers\Utils;

trait Field
{
    private $FIELDS_INDICATOR = 'fields';

    /**
     * Default fields array.
     */
    private $fields = ['*'];

    /**
     * Add dependency and pivot dependency fields to the fields array.
     *
     * @param  array  $dependencies
     * @param  array  $pivotDependencies
     * @return void
     **/
    public function addDependencyFields($dependencies, $pivotDependencies)
    {
        if (count($this->fields) > 0 && $this->fields[0] !== '*') {
            foreach ($dependencies as $dependency) {
                $field = $dependency . '_id';
                $this->addField($field);
            }

            if (count($pivotDependencies) > 0) {
                $field = 'id';
                $this->addField($field);
            }
        }
    }

    /**
     * Set the fields array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return string|void
     **/
    public function setFields($request, $model)
    {
        $fields = $request->query($this->FIELDS_INDICATOR);

        if (!is_null($fields)) {
            $this->fields = explode(',', $fields);
            return $this->validateFields($model);
        }
    }

    /**
     * Return the fields array.
     *
     * @return array
     **/
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Check if the field exists in the array of fields.
     *
     * @param  string $field
     * @return boolean
     **/
    protected function hasField($field)
    {
        return in_array($field, $this->fields);
    }

    /**
     * Add the field to the array of fields.
     *
     * @param  string $field
     * @return void
     **/
    protected function addField($field)
    {
        if (!$this->hasField($field)) {
            $this->fields[] = $field;
        }
    }

    /**
     * Verify that fields actually exist on the model.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return string|void
     **/
    private function validateFields($model)
    {
        $validFields = array_merge($model->getFillable(), ['id', 'created_at', 'updated_at']);

        foreach ($this->fields as $field) {
            if (!in_array($field, $validFields)) {
                return $field;
            }
        }
    }
}
