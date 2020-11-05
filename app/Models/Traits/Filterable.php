<?php


namespace App\Models\Traits;


trait Filterable
{
    public function scopeFilter($query, array $filters)
    {
        foreach ($filters as $key => $filter) {
            $query->when($filters[$key] ?? false, fn($query, $value) => $query->where($key, $value));
        }

        return $query;
    }
}
