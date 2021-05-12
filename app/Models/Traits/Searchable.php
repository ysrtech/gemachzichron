<?php


namespace App\Models\Traits;


use Exception;

trait Searchable
{
    public function scopeSearch($query, ?string $terms)
    {
        if (!property_exists($this, 'searchable') || !is_array($this->searchable)) {
            throw new Exception('Class ' . __CLASS__ . ' does not implement a `$searchable` array');
        }

        if (!$terms) {
            return;
        }

        $query->where(function ($query) use ($terms) {
            $query->where(function ($query) use ($terms) {
                foreach ($this->searchable as $key => $searchable) {
                    $key === array_key_first($this->searchable)
                        ? $query->where($searchable, 'like', "{$terms}%")
                        : $query->orWhere($searchable, 'like', "{$terms}%");
                }
            })->orWhere(function ($query) use ($terms) {
                collect(str_getcsv($terms, ' '))->filter()->each(function ($term) use ($query) {
                    $query->where(function ($query) use ($term) {
                        foreach ($this->searchable as $key => $searchable) {
                            $key === array_key_first($this->searchable)
                                ? $query->where($searchable, 'like', "{$term}%")
                                : $query->orWhere($searchable, 'like', "{$term}%");
                        }
                    });
                });
            });
        });


    }
}
