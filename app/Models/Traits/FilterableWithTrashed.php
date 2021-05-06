<?php


namespace App\Models\Traits;


trait FilterableWithTrashed
{
    public function scopeFilterWithTrashed($query, ?string $trashed)
    {
        return $query->when($trashed, function ($query, $trashed) {
            if ($trashed === 'with') {
                $query->withTrashed();
            } elseif ($trashed === 'only') {
                $query->onlyTrashed();
            }
        });
    }

    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where($this->getRouteKeyName(), $value)->withTrashed()->first();
    }
}
