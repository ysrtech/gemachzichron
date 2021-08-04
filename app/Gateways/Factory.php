<?php

namespace App\Gateways;

use App\Contracts\Gateway;
use Exception;
use Illuminate\Support\Str;

class Factory
{
    const ROTESSA = 'Rotessa';
    const CARDKNOX = 'Cardknox';
    const MANUAL = 'Manual';

    public function all()
    {
        return [
            self::ROTESSA,
//            self::CARDKNOX,
            self::MANUAL
        ];
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
