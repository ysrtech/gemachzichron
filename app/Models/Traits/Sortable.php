<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

trait Sortable
{
    public function scopeSort(Builder $query, ?string $columns)
    {
        if (! $columns) {
            return $query->defaultSortBy();
        }

        foreach (explode(',', $columns) as $column) {
            $direction = Str::startsWith($column, '-') ? 'desc' : 'asc';

            $column = Str::after($column, '-');

            $sortScope = 'orderBy'.Str::studly($column);

            if (method_exists(self::class, 'scope'.ucfirst($sortScope))) {
                $query->{$sortScope}($direction);
            } else {
                $query->orderBy($column, $direction);
            }
        }

        $query->defaultSortBy(); // when there are multiple records with same sort value
    }

    protected function scopeDefaultSortBy($query)
    {
        return $query->latest();
    }
}
