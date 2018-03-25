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

use Illuminate\Database\QueryException;

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

        return $this->assignValuesToModel($record, $model, $dependencies, $pivotDependencies);
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

        return $this->assignValuesToModel($record, $model, $dependencies, $pivotDependencies, true);
    }

    /**
     * Assign the given values to the updated pivot model.
     *
     * @param  array  $compositeKey
     * @param  string  $model
     * @param  array  $data
     * @param  array  $dependencies
     * @param  array  $pivotDependencies
     * @return array
     **/
    public function updatePivotModel($compositeKey, $model, $dependencies = [], $pivotDependencies = [])
    {
        $record = $model::where($compositeKey)->first();
        if (!$record) {
            $modelName = Helpers::extractModelName($model);
            $printableCompositeKey = Helpers::getPrintableCompositeKey($compositeKey);
            return ['success' => false, 'error' => "{$modelName} with {$printableCompositeKey} not found.", 'code' => 404];
        }

        return $this->assignValuesToModel($record, $model, $dependencies, $pivotDependencies, true);
    }

    /**
     * Assign the given values to the model.
     *
     * @param  string  $record
     * @param  string  $model
     * @param  array  $data
     * @param  array  $dependencies
     * @param  array  $pivotDependencies
     * @param  boolean  $update
     * @return array
     **/
    private function assignValuesToModel($record, $model, $dependencies = [], $pivotDependencies = [], $update = false)
    {
        foreach ($this->parameters as $parameter) {
            $key = $parameter->getKey();
            $value = $parameter->getValue();

            switch (true) {
                case $parameter instanceof StringParameter:
                    $record->$key = $parameter->isPassword() ? bcrypt($value) : $value;
                    break;
                case $parameter instanceof BooleanParameter:
                    $record->$key = $value;
                    break;
                case $parameter instanceof NumericParameter:
                    if ($parameter->isDependency()) {
                        $id = $value;
                        $dependency = $key;
                        $dependencyModel = $dependencies[$dependency];
                        $dependencyRecord = $dependencyModel::find($id);
                        if (!$dependencyRecord) {
                            $dependencyModelName = Helpers::extractModelName($dependencyModel);
                            return ['success' => false, 'error' => "{$dependencyModelName} with id {$id} not found.", 'code' => 404];
                        }
                        $record->{$dependency}()->associate($dependencyRecord);
                    } else {
                        $record->$key = $value;
                    }
                    break;
                case $parameter instanceof ArrayParameter:
                    $record->$key = implode(',', $value);
                    break;
                case $parameter instanceof FileParameter:
                    $result = File::save($value, $parameter->getDirectory());
                    if (!$result['success']) {
                        return ['success' => false, 'error' => $result['data'], 'code' => 500];
                    }
                    $record->$key = $result['data'];
                    break;
            }
        }

        try {
            $record->saveOrFail();
        } catch (QueryException $e) {
            $sqlStateCode = $e->errorInfo[0];
            $errorCode = $e->errorInfo[1];
            $errorMessage = $e->errorInfo[2];

            if ($sqlStateCode == '23000' && $errorCode == 1062) {
                $modelName = Helpers::extractModelName($model);
                return ['success' => false, 'error' => "{$modelName} with given ids already exists.", 'code' => 400];
            } else {
                return ['success' => false, 'error' => "${errorMessage}.", 'code' => 500];
            }
        }

        foreach ($this->pivotParameters as $parameter) {
            $key = $parameter->getKey();
            $value = $parameter->getValue();

            switch (true) {
                case $parameter instanceof ArrayParameter:
                    $pivotDependency = $key;
                    $pivotDependencyModel = $pivotDependencies[$pivotDependency];
                    $ids = [];

                    foreach ($value as $id) {
                        $pivotDependencyRecord = $pivotDependencyModel::find($id);
                        $ids[] = $pivotDependencyRecord->id;
                        if (!$pivotDependencyRecord) {
                            if (!$update) {
                                $record->delete();
                            }
                            $pivotDependencyModelName = Helpers::extractModelName($pivotDependencyModel);
                            return ['success' => false, 'error' => "{$pivotDependencyModelName} with id {$id} not found.", 'code' => 404];
                        }
                    }
                    $record->{$pivotDependency}()->sync($ids);
                    $record[$pivotDependency] = $value;
                    break;
            }
        }

        return ['success' => true, 'record' => $record];
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
