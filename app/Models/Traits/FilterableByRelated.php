<?php


namespace App\Models\Traits;


trait FilterableByRelated
{
    public function scopeFilterByRelated($query, array $filters)
    {
//        foreach ($filters as $key => $filter) {
//            $query->when(array_key_exists($key, $filters))->whereHas($key, $callback);
//        }
    }

    public function scopeFilterHasRelated($query, array $filters)
    {
        foreach ($filters as $key => $filter) {
            $query->when(
                filter_var($filter, FILTER_VALIDATE_BOOLEAN),
                fn($q) => $q->has($key),
                fn($q) => $q->doesntHave($key)
            );
        }

        return $query;
    }
}
