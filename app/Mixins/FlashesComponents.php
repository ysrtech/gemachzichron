<?php

namespace App\Mixins;

/**
 * Returns flash data which is handled in the frontend
 *
 * @example
 * return Inertia::render('Index')->banner(...)
 * return back->banner(...)
 *
 * Currently only supports RedirectResponse and Inertia\Response
 * Todo
 * replace $this->with() with session()->flash()
 * couldn't make it work on inertia render if request is coming from a redirect
 */
trait FlashesComponents
{
    /**
     * Fires a snackbar for 3500 ms (default)
     */
    public function snackbar()
    {
        return fn(string $message = 'Success', int $timeout = 3500) => $this->with("flash.snackbar", ['message' => $message, 'timeout' => $timeout]);
    }

    /**
     * Fires a banner on the top of page
     *
     * @usage
     * ...->banner('Message Here')
     * ...->banner('Message Here', 'success')
     * ...->banner('Message Here', ['level' => 'success', 'closeable' => false])
     */
    public function banner()
    {
        return fn (string $message, $options = []) => $this->with("flash.banner", array_merge(
            [
                'level'       => 'primary', // available options are: primary, success, danger, warning
                'message'     => $message,
                'closable'    => true, // whether the banner can be closed
                'action_text' => null, // will add a link to open in a new page - link text
                'action_url'  => null, // link url
            ],
            is_string($options) ? ['level' => $options] : $options
        ));
    }

    /**
     * Opens a modal
     */
    public function modal()
    {
        return fn (array $options) => $this->with("flash.modal", [
                'icon'                => $options['icon'] ?? null, // options are: success, error
                'title'               => $options['title'] ?? null,
                'message'             => $options['message'],
                'action_text'         => $options['action_text'] ?? null, // will create link/button to redirect to another route
                'action_color'        => $options['action_color'] ?? 'primary', // color of button, options are: primary, secondary, danger
                'action_route'        => $options['action_route'] ?? null, // route name
                'action_route_params' => $options['action_route_params'] ?? null,
                'action_method'       => $options['action_method'] ?? 'GET',
                'close_text'          => $options['close_text'] ?? 'Close', // text for the 'close' button
            ]
        );
    }
}
