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
    protected function setQueryParameters(Request $request, $model)
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
     * Prepare the index query based on the defined parameters and execute it.
     *
     * @param  string  $model
     * @param  array  $dependencies
     * @param  array  $pivotDependencies
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection
     **/
    protected function prepareAndExecuteIndexQuery($model, $dependencies = [], $pivotDependencies = [])
    {
        $qb = $model::query();
        $qb = $this->addOrderToQuery($qb);
        $qb = $this->addFiltersToQuery($qb);
        $this->addDependencyFields($dependencies, $pivotDependencies);
        $qb = $this->addDependencies($qb, $dependencies);
        $collection = $this->hasPagination() ? $qb->paginate($this->getPerPage(), $this->getFields()) : $qb->get($this->getFields());
        $this->addPivotDependenciesToCollection($collection, $pivotDependencies);

        return $collection;
    }

    /**
     * Prepare the show query based on the defined parameters and execute it.
     *
     * @param  integer  $id
     * @param  string  $model
     * @param  array  $dependencies
     * @param  array  $pivotDependencies
     * @return \Illuminate\Database\Eloquent\Model|null
     **/
    protected function prepareAndExecuteShowQuery($id, $model, $dependencies = [], $pivotDependencies = [])
    {
        $qb = $model::query();
        $qb = $this->addOrderToQuery($qb);
        $qb = $this->addFiltersToQuery($qb);
        $this->addDependencyFields($dependencies, $pivotDependencies);
        $qb = $this->addDependencies($qb, $dependencies);
        $record = $qb->find($id, $this->getFields());
        $this->addPivotDependenciesToRecord($record, $pivotDependencies);

        return $record;
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
    protected function setDataParameters(Request $request, $data, $dependencies = [], $pivotDependencies = [])
    {
        $validator = Validator::make($request->all(), $data);
        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        $this->setParameters($request, $data, $dependencies, $pivotDependencies);
    }

    /**
     * Prepare the store query based on the given data and execute it.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $model
     * @param  array  $dependencies
     * @param  array  $pivotDependencies
     * @return \Illuminate\Http\Response
     **/
    protected function prepareAndExecuteStoreQuery(Request $request, $model, $dependencies = [], $pivotDependencies = [])
    {
        $result = $this->createModel($model, $dependencies, $pivotDependencies);
        if (!$result['success']) {
            return response()->json($result['error'], $result['code']);
        }

        return response()->json($result['model'], 201);
    }

    /**
     * Prepare the update query based on the given data and execute it.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  integer $id
     * @param  string  $model
     * @param  array  $dependencies
     * @param  array  $pivotDependencies
     * @return \Illuminate\Http\Response
     **/
    protected function prepareAndExecuteUpdateQuery(Request $request, $id, $model, $dependencies = [], $pivotDependencies = [])
    {
        $result = $this->updateModel($id, $model, $dependencies, $pivotDependencies);
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
    protected function prepareAndExecuteDestroyQuery($id, $model) {
        if (!$model::destroy($id)) {
            $modelName = Helpers::extractModelName($model);
            return response()->json("{$modelName} with id {$id} not found.", 404);
        }

        return response()->json([], 204);
    }
}
