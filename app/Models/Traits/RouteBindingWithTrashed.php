<?php


namespace App\Models\Traits;


trait RouteBindingWithTrashed
{
    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where($this->getRouteKeyName(), $value)->withTrashed()->first();
    }
}
