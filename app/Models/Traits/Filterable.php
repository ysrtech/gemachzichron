<?php


namespace App\Models\Traits;


use Illuminate\Database\Eloquent\Builder;

trait Filterable
{
    public function scopeFilter(Builder $query, array $filters)
    {
        foreach ($filters as $key => $filter) {
            $query->where($key, $filter);
        }
    }

    public function scopeFilterBoolean(Builder $query, array $filters)
    {
        foreach ($filters as $key => $filter) {
            $query->where($key, filter_var($filter, FILTER_VALIDATE_BOOLEAN));
        }
    }

    public function scopeFilterIsNotNull(Builder $query, array $filters)
    {
        foreach ($filters as $key => $filter) {
            $query->whereNotNull($key, $filter);
        }
    }

    public function scopeFilterBetweenDates(Builder $query, string $column, ?string $startDate, ?string $endDate)
    {
        if ($startDate) {
            $query->whereDate($column, '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate($column, '<=', $endDate);
        }
    }
}
