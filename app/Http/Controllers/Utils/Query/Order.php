<?php

namespace App\Http\Controllers\Utils\Query;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait Order
{
    private $ORDER_BY_INDICATOR = 'order_by';
    private $ORDER_DIRECTION_INDICATOR = 'order_direction';
    private $VALID_ORDER_DIRECTIONS = ['asc', 'desc', 'ASC', 'DESC'];

    /**
     * Defines the attribute by which the items in the collection are sorted.
     **/
    private $orderBy = null;

    /**
     * Defines the default sorting direction of the items in the collection.
     **/
    private $orderDirection = 'asc';

    /**
     * Add sorting to the query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $qb
     * @return \Illuminate\Database\Eloquent\Builder
     **/
    public function addOrderToQuery(Builder $qb)
    {
        if (!is_null($this->orderBy)) {
            $qb = $qb->orderBy($this->orderBy, $this->orderDirection);
        }

        return $qb;
    }

    /**
     * Set the sorting attribute.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $model
     * @return string|void
     **/
    public function setOrderBy(Request $request, $model)
    {
        $value = $request->query($this->ORDER_BY_INDICATOR);

        if (!is_null($value)) {
            $validAttributes = array_merge((new $model)->getFillable(), ['id', 'created_at', 'updated_at']);

            if (!in_array($value, $validAttributes)) {
                return $value;
            }

            $this->orderBy = $value;
        }
    }

    /**
     * Return the sorting attribute.
     *
     * @return string
     **/
    public function getOrderBy()
    {
        return $this->orderBy;
    }

    /**
     * Set the sorting direction.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|void
     **/
    public function setOrderDirection(Request $request)
    {
        $value = $request->query($this->ORDER_DIRECTION_INDICATOR);

        if (!is_null($value)) {
            if (!in_array($value, $this->VALID_ORDER_DIRECTIONS)) {
                return $value;
            }

            $this->orderDirection = $value;
        }
    }

    /**
     * Return the sorting direction.
     *
     * @return string
     **/
    public function getOrderDirection()
    {
        return $this->orderDirection;
    }
}
