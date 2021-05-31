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

    public function scopeFilterNull(Builder $query, array $filters)
    {
        foreach ($filters as $key => $filter) {
            filter_var($filter, FILTER_VALIDATE_BOOLEAN)
                ? $query->whereNotNull($key)
                : $query->whereNull($key);
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
