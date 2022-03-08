<?php

namespace Bhekor\LaravelFlutterwave;

use Illuminate\Support\ServiceProvider;

class FlutterwaveServiceProvider extends ServiceProvider
{
    protected $defer = false;

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $config = realpath(__DIR__ . '/../resources/config/flutterwave.php');

        $this->publishes([
            $config => config_path('flutterwave.php')
        ]);
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {

        $this->app->singleton('laravelflutterwave', function ($app) {

            return new Flutterwave($app->make("request"));
        });

        $this->app->alias('laravelflutterwave', "Bhekor\Flutterwave\Flutterwave");
    }

    /**
     * Get the services provided by the provider
     *
     * @return array
     */
    public function provides()
    {
        return ['laravelflutterwave'];
    }
}