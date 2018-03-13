<?php

namespace App\Http\Controllers\Utils\Data;

use Illuminate\Http\Request;
use App\Http\Controllers\Utils\File;
use App\Http\Controllers\Utils\Helpers;
use App\Http\Controllers\Utils\Data\ArrayParameter;
use App\Http\Controllers\Utils\Data\AudioParameter;
use App\Http\Controllers\Utils\Data\BooleanParameter;
use App\Http\Controllers\Utils\Data\FileParameter;
use App\Http\Controllers\Utils\Data\ImageParameter;
use App\Http\Controllers\Utils\Data\NumericParameter;
use App\Http\Controllers\Utils\Data\StringParameter;

trait Parameters
{
    /**
     * Placeholder array for parameters.
     */
    private $parameters = [];

    /**
     * Placeholder array for pivot parameters.
     */
    private $pivotParameters = [];

    /**
     * Assign the given values to the newly created model.
     *
     * @param  string  $model
     * @param  array  $data
     * @param  array  $dependencies
     * @param  array  $pivotDependencies
     * @return array
     **/
    public function createModel($model, $dependencies = [], $pivotDependencies = [])
    {
        $record = new $model;

        return $this->assignValuesToModel($record, $dependencies, $pivotDependencies);
    }

    /**
     * Assign the given values to the updated model.
     *
     * @param  integer  $id
     * @param  string  $model
     * @param  array  $data
     * @param  array  $dependencies
     * @param  array  $pivotDependencies
     * @return array
     **/
    public function updateModel($id, $model, $dependencies = [], $pivotDependencies = [])
    {
        $record = $model::find($id);
        if (!$record) {
            $modelName = Helpers::extractModelName($model);
            return ['success' => false, 'error' => "{$modelName} with id {$id} not found.", 'code' => 404];
        }

        return $this->assignValuesToModel($record, $dependencies, $pivotDependencies, true);
    }

    /**
     * Assign the given values to the model.
     *
     * @param  string  $model
     * @param  array  $data
     * @param  array  $dependencies
     * @param  array  $pivotDependencies
     * @param  boolean  $update
     * @return array
     **/
    private function assignValuesToModel($model, $dependencies = [], $pivotDependencies = [], $update = false)
    {
        foreach ($this->parameters as $parameter) {
            $key = $parameter->getKey();
            $value = $parameter->getValue();

            switch (true) {
                case $parameter instanceof StringParameter:
                    $model->$key = $parameter->isPassword() ? bcrypt($value) : $value;
                    break;
                case $parameter instanceof BooleanParameter:
                    $model->$key = $value;
                    break;
                case $parameter instanceof NumericParameter:
                    if ($parameter->isDependency()) {
                        $id = $value;
                        $dependency = $key;
                        $dependencyModel = $dependencies[$dependency];
                        $record = $dependencyModel::find($id);
                        if (!$record) {
                            $dependencyModelName = Helpers::extractModelName($dependencyModel);
                            return ['success' => false, 'error' => "{$dependencyModelName} with id {$id} not found.", 'code' => 404];
                        }
                        $model->{$dependency}()->associate($record);
                    } else {
                        $model->$key = $value;
                    }
                    break;
                case $parameter instanceof ArrayParameter:
                    $model->$key = implode(',', $value);
                    break;
                case $parameter instanceof FileParameter:
                    $result = File::save($value, $parameter->getDirectory());
                    if (!$result['success']) {
                        return ['success' => false, 'error' => $result['data'], 'code' => 500];
                    }
                    $model->$key = $result['data'];
                    break;
            }
        }

        $model->saveOrFail();

        foreach ($this->pivotParameters as $parameter) {
            $key = $parameter->getKey();
            $value = $parameter->getValue();

            switch (true) {
                case $parameter instanceof ArrayParameter:
                    $pivotDependency = $key;
                    $pivotDependencyModel = $pivotDependencies[$pivotDependency];
                    $ids = [];

                    foreach ($value as $id) {
                        $record = $pivotDependencyModel::find($id);
                        $ids[] = $record->id;
                        if (!$record) {
                            if (!$update) {
                                $model->delete();
                            }
                            $pivotDependencyModelName = Helpers::extractModelName($pivotDependencyModel);
                            return ['success' => false, 'error' => "{$pivotDependencyModelName} with id {$id} not found.", 'code' => 404];
                        }
                    }
                    var_dump($ids);
                    var_dump($pivotDependency);
                    var_dump($pivotDependencyModel);
                    die;
                    $model->{$pivotDependency}()->sync($ids);
                    $model[$pivotDependency] = $value;
                    break;
            }
        }

        return ['success' => true, 'model' => $model];
    }

    /**
     * Set the parameters and pivot parameters array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $data
     * @param  array  $dependencies
     * @param  array  $pivotDependencies
     * @return void
     **/
    public function setParameters(Request $request, $data, $dependencies, $pivotDependencies)
    {
        foreach ($data as $key => $rules) {
            if (!$request->has($key)) {
                continue;
            }

            if (is_array($rules)) {
                $properties = [];
                foreach ($rules as $rule) {
                    if (is_string($rule)) {
                        $properties[] = $rule;
                    }
                }
            } else {
                $properties = explode('|', $rules);
            }

            switch (true) {
                case in_array('string', $properties):
                    $parameter = new StringParameter;
                    $parameter->setKey($key);
                    $parameter->setValue($request->get($key));
                    $parameter->setPassword($key == 'password');
                    $this->parameters[] = $parameter;
                    break;
                case in_array('boolean', $properties):
                    $parameter = new BooleanParameter;
                    $parameter->setKey($key);
                    $parameter->setValue($request->get($key));
                    $this->parameters[] = $parameter;
                    break;
                case in_array('integer', $properties):
                case in_array('numeric', $properties):
                    $parameter = new NumericParameter;
                    $parameter->setKey(Helpers::removeIdSuffix($key));
                    $parameter->setValue($request->get($key));
                    $parameter->setDependency(in_array($parameter->getKey(), array_keys($dependencies)));
                    $this->parameters[] = $parameter;
                    break;
                case in_array('array', $properties):
                    $parameter = new ArrayParameter;
                    $parameter->setKey($key);
                    $parameter->setValue($request->get($key));
                    $parameter->setPivotDependency(in_array($key, array_keys($pivotDependencies)));
                    if ($parameter->isPivotDependency()) {
                        $this->pivotParameters[] = $parameter;
                    } else {
                        $this->parameters[] = $parameter;
                    }
                    break;
                case in_array('image', $properties):
                    $parameter = new ImageParameter;
                    $parameter->setKey($key);
                    $parameter->setValue($request->file($key));
                    $this->parameters[] = $parameter;
                    break;
                case in_array('audio', $properties):
                    $parameter = new AudioParameter;
                    $parameter->setKey($key);
                    $parameter->setValue($request->file($key));
                    $this->parameters[] = $parameter;
                    break;
                case in_array('file', $properties):
                    $parameter = new FileParameter;
                    $parameter->setKey($key);
                    $parameter->setValue($request->file($key));
                    $this->parameters[] = $parameter;
                    break;
            }
        }
    }

    /**
     * Return the parameters array.
     *
     * @return int
     **/
    public function getParameters()
    {
        return $this->parameters;
    }
}
