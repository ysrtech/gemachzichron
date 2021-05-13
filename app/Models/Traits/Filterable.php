<?php


namespace App\Models\Traits;


trait Filterable
{
    public function scopeFilter($query, array $filters)
    {
        foreach ($filters as $key => $filter) {
            $query->where($key, $filter);
        }
    }

    public function scopeFilterBoolean($query, array $filters)
    {
        foreach ($filters as $key => $filter) {
            $query->where($key, filter_var($filter, FILTER_VALIDATE_BOOLEAN));
        }
    }

    public function scopeFilterBetweenDates($query, string $column, ?string $startDate, ?string $endDate)
    {
        if ($startDate) {
            $query->whereDate($column, '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate($column, '<=', $endDate);
        }
    }
}
