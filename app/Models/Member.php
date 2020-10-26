<?php

namespace App\Models;

use App\Models\Traits\RouteBindingWithTrashed;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Member extends Model
{
    use HasFactory, SoftDeletes, RouteBindingWithTrashed;

    protected $guarded = [];

    public function scopeFilter($query, array $filters)
    {
        return $query
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where('last_name', 'like', "{$search}%")
                    ->orWhere('first_name', 'like', "{$search}%")
                    ->orWhere('hebrew_name', 'like', "{$search}%")
                    ->orWhere('email', 'like', "{$search}%");
            })
            ->when($filters['archived'] ?? null, function ($query, $trashed) {
                if ($trashed === 'with') {
                    $query->withTrashed();
                } elseif ($trashed === 'only') {
                    $query->onlyTrashed();
                }
            });
    }
}
