<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Controllers\Utils\Dependency;
use App\Http\Controllers\Utils\Field;
use App\Http\Controllers\Utils\Filter;
use App\Http\Controllers\Utils\Order;
use App\Http\Controllers\Utils\Pagination;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, Dependency, Field, Filter, Order, Pagination;

    const VALID_QUERY_PARAMETERS = ['per_page', 'page', 'order_by', 'order_direction', 'fields'];

    /**
     * Extract, validate and set the query parameters.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return \Illuminate\Http\Response|void
     **/
    protected function setParameters($request, $model)
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
     * @param  \Illuminate\Database\Eloquent\Builder  $qb
     * @param  array  $dependencies
     * @param  array  $pivotDependencies
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection
     **/
    protected function prepareAndExecuteIndexQuery($qb, $dependencies = [], $pivotDependencies = [])
    {
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
     * @param  int  $id
     * @param  \Illuminate\Database\Eloquent\Builder  $qb
     * @param  array  $dependencies
     * @param  array  $pivotDependencies
     * @return \Illuminate\Database\Eloquent\Model|null
     **/
    protected function prepareAndExecuteShowQuery($id, $qb, $dependencies = [], $pivotDependencies = [])
    {
        $qb = $this->addOrderToQuery($qb);
        $qb = $this->addFiltersToQuery($qb);
        $this->addDependencyFields($dependencies, $pivotDependencies);
        $qb = $this->addDependencies($qb, $dependencies);
        $record = $qb->find($id, $this->getFields());
        $this->addPivotDependenciesToRecord($record, $pivotDependencies);

        return $record;
    }
}
