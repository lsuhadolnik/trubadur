<?php

namespace App\Http\Controllers\Utils\Query;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

trait Dependency
{
    /**
     * Add dependencies to the query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $qb
     * @param  array  $dependencies
     * @return \Illuminate\Database\Eloquent\Builder
     **/
    public function addDependencies(Builder $qb, $dependencies)
    {
        foreach ($dependencies as $dependency => $model) {
            $qb = $qb->with($dependency);
        }

        return $qb;
    }

    /**
     * Add pivot dependencies to the collection.
     *
     * @param  \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection  $collection
     * @param  array  $pivotDependencies
     * @return void
     **/
    public function addPivotDependenciesToCollection($collection, $pivotDependencies)
    {
        foreach ($pivotDependencies as $pivotDependency => $model) {
            foreach ($collection as $record) {
                $record[$pivotDependency] = $record->{$pivotDependency}()->pluck('id');
            }
        }
    }

    /**
     * Add pivot dependencies to the record.
     *
     * @param  \Illuminate\Database\Eloquent\Model|null  $record
     * @param  array  $pivotDependencies
     * @return void
     **/
    public function addPivotDependenciesToRecord($record, $pivotDependencies)
    {
        if ($record) {
            foreach ($pivotDependencies as $pivotDependency => $model) {
                $record[$pivotDependency] = $record->{$pivotDependency}()->pluck('id');
            }
        }
    }
}
