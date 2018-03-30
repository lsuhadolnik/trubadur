<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Controllers\Utils\Helpers;
use App\Http\Controllers\Utils\Data\Parameters;
use App\Http\Controllers\Utils\Query\Dependency;
use App\Http\Controllers\Utils\Query\Field;
use App\Http\Controllers\Utils\Query\Filter;
use App\Http\Controllers\Utils\Query\Order;
use App\Http\Controllers\Utils\Query\Pagination;
use Validator;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, Dependency, Field, Filter, Order, Pagination, Parameters;

    const VALID_QUERY_PARAMETERS = ['per_page', 'page', 'order_by', 'order_direction', 'fields'];

    /**
     * Extract, validate and set the query parameters.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $model
     * @return \Illuminate\Http\Response|void
     **/
    private function setQueryParameters(Request $request, $model)
    {
        foreach ($request->query() as $key => $value) {
            if (!$this->isFilter($key) && !in_array($key, self::VALID_QUERY_PARAMETERS)) {
                return "'{$key}' is not a valid query parameter.";
            }
        }

        $error = $this->setPerPage($request);
        if ($error) {
            return "'{$error}' is not a valid per page number.";
        }

        $error = $this->setOrderBy($request, $model);
        if ($error) {
            return "'{$error}' is not a valid sorting attribute.";
        }

        $error = $this->setOrderDirection($request);
        if ($error) {
            return "'{$error}' is not a valid sotring direction.";
        }

        $error = $this->setFilters($request, $model);
        if ($error) {
            return "'{$error}' is not a valid query parameter.";
        }

        $error = $this->setFields($request, $model);
        if ($error) {
            return "Field '{$error}' does not exist.";
        }
    }

    /**
     * Extract, validate and set the request data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $data
     * @param  array  $dependencies
     * @param  array  $pivotDependencies
     * @return array|void
     **/
    private function setDataParameters(Request $request, $data, $dependencies = [], $pivotDependencies = [])
    {
        $validator = Validator::make($request->all(), $data);
        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        $this->setParameters($request, $data, $dependencies, $pivotDependencies);
    }

    /**
     * Prepare the index query based on the defined parameters and execute it.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $model
     * @param  array  $dependencies
     * @param  array  $pivotDependencies
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection
     **/
    protected function prepareAndExecuteIndexQuery(Request $request, $model, $dependencies = [], $pivotDependencies = [])
    {
        $error = $this->setQueryParameters($request, $model);
        if ($error) {
            return response()->json($error, 400);
        }

        $qb = $model::query();
        $qb = $this->addOrderToQuery($qb);
        $qb = $this->addFiltersToQuery($qb);
        $this->addDependencyFields($dependencies, $pivotDependencies);
        $qb = $this->addDependencies($qb, $dependencies);
        $collection = $this->hasPagination() ? $qb->paginate($this->getPerPage(), $this->getFields()) : $qb->get($this->getFields());
        $this->addPivotDependenciesToCollection($collection, $pivotDependencies);

        return response()->json($collection, 200);
    }

    /**
     * Prepare the show query based on the defined parameters and execute it.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  integer  $id
     * @param  string  $model
     * @param  array  $dependencies
     * @param  array  $pivotDependencies
     * @return \Illuminate\Database\Eloquent\Model|null
     **/
    protected function prepareAndExecuteShowQuery(Request $request, $id, $model, $dependencies = [], $pivotDependencies = [])
    {
        $error = $this->setQueryParameters($request, $model);
        if ($error) {
            return response()->json($error, 400);
        }

        $qb = $model::query();
        $qb = $this->addOrderToQuery($qb);
        $qb = $this->addFiltersToQuery($qb);
        $this->addDependencyFields($dependencies, $pivotDependencies);
        $qb = $this->addDependencies($qb, $dependencies);
        $record = $qb->find($id, $this->getFields());
        if (!$record) {
            $modelName = Helpers::extractModelName($model);
            return response()->json("{$modelName} with id {$id} not found.", 404);
        }
        $this->addPivotDependenciesToRecord($record, $pivotDependencies);

        return response()->json($record, 200);
    }

    /**
     * Prepare the show query for a pivot table based on the defined parameters and execute it.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $compositeKey
     * @param  string  $model
     * @param  array  $dependencies
     * @param  array  $pivotDependencies
     * @return \Illuminate\Database\Eloquent\Model|null
     **/
    protected function prepareAndExecutePivotShowQuery(Request $request, $compositeKey, $model, $dependencies = [], $pivotDependencies = [])
    {
        $error = $this->setQueryParameters($request, $model);
        if ($error) {
            return response()->json($error, 400);
        }

        $qb = $model::query();
        $qb = $this->addOrderToQuery($qb);
        $qb = $this->addFiltersToQuery($qb);
        $this->addDependencyFields($dependencies, $pivotDependencies);
        $qb = $this->addDependencies($qb, $dependencies);
        $record = $qb->where($compositeKey)->first($this->getFields());
        if (!$record) {
            $modelName = Helpers::extractModelName($model);
            $printableCompositeKey = Helpers::getPrintableCompositeKey($compositeKey);
            return response()->json("{$modelName} with {$printableCompositeKey} not found.", 404);
        }
        $this->addPivotDependenciesToRecord($record, $pivotDependencies);

        return response()->json($record, 200);
    }

    /**
     * Prepare the store query based on the given data and execute it.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $data
     * @param  string  $model
     * @param  array  $dependencies
     * @param  array  $pivotDependencies
     * @return \Illuminate\Http\Response
     **/
    protected function prepareAndExecuteStoreQuery(Request $request, $data, $model, $dependencies = [], $pivotDependencies = [])
    {
        $error = $this->setDataParameters($request, $data, $dependencies, $pivotDependencies);
        if ($error) {
            return response()->json($error, 422);
        }

        $result = $this->createModel($model, $dependencies, $pivotDependencies);
        if (!$result['success']) {
            return response()->json($result['error'], $result['code']);
        }

        return response()->json($result['record'], 201);
    }

    /**
     * Prepare the update query based on the given data and execute it.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $data
     * @param  integer $id
     * @param  string  $model
     * @param  array  $dependencies
     * @param  array  $pivotDependencies
     * @return \Illuminate\Http\Response
     **/
    protected function prepareAndExecuteUpdateQuery(Request $request, $data, $id, $model, $dependencies = [], $pivotDependencies = [])
    {
        $error = $this->setDataParameters($request, $data, $dependencies, $pivotDependencies);
        if ($error) {
            return response()->json($error, 422);
        }

        $result = $this->updateModel($id, $model, $dependencies, $pivotDependencies);
        if (!$result['success']) {
            return response()->json($result['error'], $result['code']);
        }

        return response()->json([], 204);
    }

    /**
     * Prepare the update query for a pivot table based on the given data and execute it.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $data
     * @param  array  $compositeKey
     * @param  string  $model
     * @param  array  $dependencies
     * @param  array  $pivotDependencies
     * @return \Illuminate\Http\Response
     **/
    protected function prepareAndExecutePivotUpdateQuery(Request $request, $data, $compositeKey, $model, $dependencies = [], $pivotDependencies = [])
    {
        $error = $this->setDataParameters($request, $data, $dependencies, $pivotDependencies);
        if ($error) {
            return response()->json($error, 422);
        }

        $result = $this->updatePivotModel($compositeKey, $model, $dependencies, $pivotDependencies);
        if (!$result['success']) {
            return response()->json($result['error'], $result['code']);
        }

        return response()->json([], 204);
    }

    /**
     * Prepare the destroy query based on the given data and execute it.
     *
     * @param  integer $id
     * @param  string  $model
     * @return \Illuminate\Http\Response
     **/
    protected function prepareAndExecuteDestroyQuery($id, $model)
    {
        if (!$model::destroy($id)) {
            $modelName = Helpers::extractModelName($model);
            return response()->json("{$modelName} with id {$id} not found.", 404);
        }

        return response()->json([], 204);
    }

    /**
     * Prepare the destroy query for a pivot table based on the given data and execute it.
     *
     * @param  array  $compositeKey
     * @param  string  $model
     * @return \Illuminate\Http\Response
     **/
    protected function prepareAndExecutePivotDestroyQuery($compositeKey, $model)
    {
        $record = $model::where($compositeKey)->first();
        if (!$record) {
            $modelName = Helpers::extractModelName($model);
            $printableCompositeKey = Helpers::getPrintableCompositeKey($compositeKey);
            return response()->json("{$modelName} with {$printableCompositeKey} not found.", 404);
        }

        $model::where($compositeKey)->delete();

        return response()->json([], 204);
    }
}
