<?php

namespace App\Providers;

use App\Gateways\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

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

        RedirectResponse::macro('flash', fn(string $key, $options) => $this->with("flash.$key", $options));

        Response::macro('flash', fn(string $key, $options) => $this->with("flash.$key", $options));

        Inertia::macro('flash', function (string $key, $options, string $defaultPage, array $defaultPageProps = [], int $statusCode = 200) {
            $inertiaResponse = Inertia::render($defaultPage, $defaultPageProps);

            if ($inertiaResponse->toResponse(request()) instanceof JsonResponse) {
                // triggers an 'invalid' inertia response, which is handled (displayed) in the frontend
                return new JsonResponse(['flash' => [$key => $options],], $statusCode);
            }

            return $inertiaResponse->flash($key, $options);
        });

        /* useful shortcuts for above macros */
        RedirectResponse::macro('snackbar', fn(string $message = 'Success', int $timeout = 3500) => $this->flash('snackbar', ['message' => $message, 'timeout' => $timeout]));
        RedirectResponse::macro('banner', fn(string $message, $options = []) => $this->flash('banner', array_merge(['message' => $message], is_string($options) ? ['level' => $options] : $options)));
        RedirectResponse::macro('login', fn(array $options = []) => $this->flash('login_modal', $options));
        RedirectResponse::macro('confirmPassword', fn(array $options = []) => $this->flash('confirm_password_modal', $options));
        RedirectResponse::macro('alert', fn(array $options) => $this->flash('alert_modal', $options));
        Response::macro('snackbar', fn(string $message = 'Success', int $timeout = 3500) => $this->flash('snackbar', ['message' => $message, 'timeout' => $timeout]));
        Response::macro('banner', fn(string $message, $options = []) => $this->flash('banner', array_merge(['message' => $message], is_string($options) ? ['level' => $options] : $options)));
        Response::macro('login', fn(array $options = []) => $this->flash('login_modal', $options));
        Response::macro('confirmPassword', fn(array $options = []) => $this->flash('confirm_password_modal', $options));
        Response::macro('alert', fn(array $options) => $this->flash('alert_modal', $options));
    }
}
