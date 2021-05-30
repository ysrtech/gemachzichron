<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array all()
 * @method static \App\Contracts\Gateway initialize(string $class)
 *
 * @see \App\Gateways\Factory
 */
class Gateway extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'gateway';
    }
}
