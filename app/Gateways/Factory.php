<?php

namespace App\Gateways;

use App\Contracts\Gateway;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Factory
{
    public function all()
    {
        return Arr::pluck(config('gateways'), 'name');
    }

    public function initialize(string $class, ...$args): Gateway
    {
        $class = 'App\Gateways\\' . Str::studly($class) . '\\Gateway';

        if (!class_exists($class)) {
            throw new Exception("Class '$class' not found");
        }

        return new $class(...$args);
    }
}
