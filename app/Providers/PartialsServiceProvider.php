<?php

namespace App\Providers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use Inertia\Response;

class PartialsServiceProvider extends ServiceProvider
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
        /**
         * share partial from redirect response flash
         */
        Inertia::share([
            'partial' => fn() => Session::get('partial'),
        ]);

        /**
         * Render partial only. Persist the current page
         * @usage Inertia::partial('Sections/SomeModal', ['someProp' => '...'], 'Dashboard')
         *
         * @param string $component the partial component
         * @param array $props partial props
         * @param string $initialPage page to render when response is initial page load
         * @param array $initialPageProps initial page props
         */
        Inertia::macro('partial', function (string $component, array $props, string $initialPage, array $initialPageProps = [], int $statusCode = 200) {
            $inertiaResponse = Inertia::render($initialPage, $initialPageProps);

            if ($inertiaResponse->toResponse(request()) instanceof JsonResponse) {
                // triggers an 'invalid' inertia response, which is
                // handled in the frontend by displaying the partial
                return new JsonResponse([
                    'partial' => compact('component', 'props'),
                ], $statusCode);
            }

            return $inertiaResponse->withPartial($component, $props);
        });

        /**
         * Render a new page with the partial
         * @usage Inertia::render('Dashboard')->withPartial('@/Sections/SomeModal', ['someProp' => '...'])
         *
         * @param string $partial the partial component
         * @param array $props partial props
         */
        Response::macro('withPartial', function (string $component, array $props = []) {
            return $this->with(['partial' => compact('component', 'props')]);
        });

        /**
         * Redirect response with the partial (flashes partial data),
         *
         * @usage back()->withPartial('@/Sections/SomeModal', ['someProp' => '...'])
         *
         * @param string $component the partial component
         * @param array $props partial props
         */
        RedirectResponse::macro('withPartial', function (string $component, array $props = []) {
            session()->flash('partial', compact('component', 'props'));
            return $this;
        });

        /*
        |-------------------------------------------
        | Useful shortcut macros for partials
        |-------------------------------------------
        */
        $snackbar = fn(string $message = 'Success', int $timeout = 3500) => $this->withPartial('Snackbar', ['message' => $message, 'timeout' => $timeout]);

        /**
         * Displays a banner on top of the page
         *
         * @param string $message
         * @param string|array $options (string will be level only, all other options will use their default value)
         * @return mixed
         */
        $banner = fn(string $message, $options = []) => $this->withPartial('Banner', array_merge(
            ['message' => $message],
            is_string($options) ? ['level' => $options] : $options
        ));

        RedirectResponse::macro('snackbar', $snackbar);
        RedirectResponse::macro('banner', $banner);
        Response::macro('snackbar', $snackbar);
        Response::macro('banner', $banner);
    }
}
