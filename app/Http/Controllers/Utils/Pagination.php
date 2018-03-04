<?php

namespace App\Http\Controllers\Utils;

trait Pagination
{
    private $PER_PAGE_INDICATOR = 'per_page';

    /**
     * Defines the default number of records per page returned from paginator.
     **/
    private $perPage = 15;

    /**
     * Execute the pagination query with records of the collection containing the requested fields.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $qb
     * @param  array  $fields
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     **/
    public function executeWithPagination($qb, $fields) {
        return is_null($fields) ? $qb->paginate($this->perPage) : $qb->paginate($this->perPage, $fields);
    }

    /**
     * Set the number of records in a collection that will be returned per single page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|void
     **/
    public function setPerPage($request)
    {
        $value = $request->query($this->PER_PAGE_INDICATOR);

        if (!is_null($value)) {
            if (!is_numeric($value)) {
                return $value;
            }

            $this->perPage = $value === '0' ? null : intval($value);
        }
    }

    /**
     * Return the number of records in a collection that will be returned per single page.
     *
     * @return int
     **/
    public function getPerPage()
    {
        return $this->perPage;
    }

    /**
     * Indicate whether the collection should be paginated.
     *
     * @return boolean
     **/
    public function hasPagination()
    {
        return !is_null($this->perPage);
    }
}
