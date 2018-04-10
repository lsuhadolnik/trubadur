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
     * Defines the attribute(s) by which the items in the collection are sorted.
     **/
    private $orderBy = null;

    /**
     * Defines the default sorting direction(s) of the items in the collection.
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
            for ($i = 0; $i < count($this->orderBy); $i++) {
                $qb = $qb->orderBy($this->orderBy[$i], is_array($this->orderDirection) ? $this->orderDirection[$i] : $this->orderDirection);
            }
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

            $tokens = explode(',', $value);
            foreach ($tokens as $token) {
                if (!in_array($token, $validAttributes)) {
                    return $token;
                }
            }

            $this->orderBy = $tokens;
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
            $tokens = explode(',', $value);
            foreach ($tokens as $token) {
                if (!in_array($token, $this->VALID_ORDER_DIRECTIONS)) {
                    return $token;
                }
            }

            $this->orderDirection = $tokens;
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
