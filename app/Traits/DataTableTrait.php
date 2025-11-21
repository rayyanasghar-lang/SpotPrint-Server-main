<?php
namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

trait DataTableTrait
{
    /**
     * @param Builder $query
     * @param array $searchColumns
     * @param $filters
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    private function dataTable(Builder $query, array $searchColumns = [], $filters = [])
    {
        /* Search */
        if ($searchColumns && request('search')) {
            $search = request('search');
            $query->where(function ($query) use ($searchColumns, $search) {
                foreach ($searchColumns as $columnName) {
                    $query->orWhere($columnName, 'like', "%{$search}%");

                    /* if (strpos($columnName, '.') !== false) {
                        // Handle related table search
                        [$relation, $column] = explode('.', $columnName);
                        $query->orWhereHas($relation, function ($subQuery) use ($column, $search) {
                            $subQuery->where($column, 'like', "%{$search}%");
                        });
                    } else {
                        $query->orWhere($columnName, 'like', "%{$search}%");
                    } */
                }
            });
        }

        /* Sortable */
        $query->when(request('sortBy'), function ($query) {
            $query->orderBy(request('sortBy'), request('orderBy', 'ASC'));
        }, function ($query) {
            $query->orderBy('id', 'DESC');
        });

        /*Filters*/
        if (! empty($filters)) {
            foreach ($filters as $filterKey => $filterVal) {
                if (isset($filterVal)) {
                    if (is_object($filterVal)) {
                        $query->whereHas($filterKey, function ($query) use ($filterVal) {
                            foreach ($filterVal as $key => $val) {
                                if (! empty($val)) {
                                    $query->where($key, $val);
                                }

                            }
                        });
                    } else {
                        if (str_contains($filterVal, ' to ')) {
                            [$startDate, $endDate] = explode(' to ', $filterVal);
                            $query->where($filterKey, '>=', $startDate)
                                ->where($filterKey, '<=', $endDate);
                        } else {
                            if (str_contains($filterKey, 'created_at')) {
                                $query->whereDate($filterKey, '=', Carbon::parse($filterVal)->format('Y-m-d'));
                            } else {
                                $query->where($filterKey, $filterVal);
                            }
                        }
                    }
                }
            }
        }

        if (! request('itemsPerPage') || request('itemsPerPage') === 'all') {
            return $query->get();
        } else {
            return $query->paginate(request('itemsPerPage'), ['*'], 'page', request('page'));
        }

    }
}
