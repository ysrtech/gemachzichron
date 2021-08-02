<?php


namespace App\Models\Traits;


trait FilterableByRelated
{
    public function scopeFilterByRelated($query, array $filters, string $relation)
    {
        $query->whereHas($relation, function ($q) use ($filters) {
            foreach ($filters as $key => $filter) {
                $q->where($key, $filter);
            }
        });
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
