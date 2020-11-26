<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        Model::unguard();

        RedirectResponse::macro('snackbar', fn(string $message = 'Success!') => $this->with("flash.snackbar", $message));
        RedirectResponse::macro('banner', fn(string $message, string $level = 'primary') => $this->with("flash.banner.{$level}", $message));
    }
}
