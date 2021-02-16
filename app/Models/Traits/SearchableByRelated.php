<?php


namespace App\Models\Traits;


use Exception;

trait SearchableByRelated
{
    public function scopeSearchByRelated($query, ?string $search, array $relations)
    {
        return $query->when($search, function ($query, $search) use ($relations) {
            foreach ($relations as $key => $relation) {
                $key === array_key_first($relations)
                    ? $query->whereHas($relation, fn($q) => $q->search($search))
                    : $query->orWhereHas($relation, fn($q) => $q->search($search));
            }
        });
    }
}
