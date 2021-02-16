<?php


namespace App\Models\Traits;


use Exception;

trait Searchable
{
    public function scopeSearch($query, ?string $search)
    {
        if (!property_exists($this, 'searchable') || !is_array($this->searchable)) {
            throw new Exception('Class ' . __CLASS__ . ' does not implement a `$searchable` array');
        }

        return $query->when($search, function ($query, $search) {
            foreach ($this->searchable as $key => $searchable) {
                $key === array_key_first($this->searchable)
                    ? $query->where($searchable, 'like', "{$search}%")->orWhere($searchable, 'like', "% {$search}%")
                    : $query->orWhere($searchable, 'like', "{$search}%")->orWhere($searchable, 'like', "% {$search}%");
            }
        });
    }
}
