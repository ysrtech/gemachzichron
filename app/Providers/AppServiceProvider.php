<?php

namespace App\Providers;

use App\Gateways\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('gateway', fn() => new Factory());
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

        $this->app->resolving(
            LengthAwarePaginator::class,
            fn($paginator) => $paginator->appends(Request::all())
        );

        Str::macro('model', function (string $model) {
            $model = (string) Str::of($model)
                ->singular()
                ->studly()
                ->start('App\Models\\');

            abort_unless(class_exists($model), 404, "class $model does not exist");

            return $model;
        });
    }
}
