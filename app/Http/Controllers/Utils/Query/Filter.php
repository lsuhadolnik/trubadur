<?php

namespace App\Http\Controllers\Utils\Query;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Http\Controllers\Utils\Helpers;

trait Filter
{
    private $FILTER_INDICATOR = 'filter_';
    private $FILTER_ID_INDICATOR = 'id';

    /**
     * Placeholder filters array.
     */
    private $filters = [];

    /**
     * Add filters to the query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $qb
     * @return \Illuminate\Database\Eloquent\Builder
     **/
    public function addFiltersToQuery(Builder $qb)
    {
        foreach ($this->filters as $key => $value) {
            if (Helpers::endsWith($key, $this->FILTER_ID_INDICATOR)) {
                $qb = $qb->whereIn($key, $value);
            } else {
                $qb = $qb->where($key, 'like', '%' . $value . '%');
            }
        }

        return $qb;
    }

    /**
     * Set the filters array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $model
     * @return string|void
     **/
    public function setFilters(Request $request, $model)
    {
        $validFilters = array_merge((new $model)->getFillable(), ['id', 'created_at', 'updated_at']);

        foreach ($request->query() as $key => $value) {
            if (Helpers::startsWith($key, $this->FILTER_INDICATOR)) {
                $filter = $this->extractFilterName($key);

                if (!in_array($filter, $validFilters)) {
                    return $key;
                }

                if (Helpers::endsWith($filter, $this->FILTER_ID_INDICATOR)) {
                    $this->filters[$filter] = explode(',', $value);
                } else {
                    $this->filters[$filter] = urldecode($value);
                }
            }
        }
    }

    /**
     * Return the filters array.
     *
     * @return array
     **/
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * Check if the given string is a fitler attribute.
     *
     * @param  string  $input
     * @return boolean
     **/
    public function isFilter($input)
    {
        return Helpers::startsWith($input, $this->FILTER_INDICATOR);
    }

    /**
     * Extract the filter attribute name.
     *
     * @param  string  $input
     * @return string
     **/
    private function extractFilterName($input)
    {
        return substr($input, strlen($this->FILTER_INDICATOR));
    }
}
