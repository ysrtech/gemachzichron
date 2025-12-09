<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class PlanType extends Model
{
    public $timestamps = false;

    protected $casts = [
        'rates' => 'array'
    ];

    protected static function booted()
    {
        static::saved(fn($planType) => Cache::forget('plan-types'));
        static::deleted(fn($planType) => Cache::forget('plan-types'));
    }

    public function members()
    {
        return $this->hasMany(Member::class);
    }
}
