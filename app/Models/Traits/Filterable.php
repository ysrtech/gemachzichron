<?php


namespace App\Models\Traits;


trait Filterable
{
    public function scopeFilter($query, array $filters)
    {
        foreach ($filters as $key => $filter) {
            $query->when(isset($filters[$key]), fn($q) => $q->where($key, $filters[$key]));
        }

        return $query;
    }
}
