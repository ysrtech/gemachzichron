<?php

namespace App\Providers;

use App\Mail\Transport\ResendTransport;
use Illuminate\Mail\MailManager;
use Illuminate\Support\ServiceProvider;

class ResendMailServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        $this->app->extend('mail.manager', function (MailManager $manager) {
            $manager->extend('resend', function () {
                $config = $this->app['config']['mail.mailers.resend'];
                return new ResendTransport($config['api_key']);
            });

            return $manager;
        });
    }
}
