<?php

namespace App\Providers;

use App\Mixins\InertiaResponseMixin;
use App\Mixins\RedirectResponseMixin;
use Illuminate\Support\ServiceProvider;

class MixinServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        \Inertia\Response::mixin(new InertiaResponseMixin());
        \Illuminate\Http\RedirectResponse::mixin(new RedirectResponseMixin());
    }
}
