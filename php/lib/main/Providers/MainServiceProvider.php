<?php

namespace Main\Providers;

use Illuminate\Support\ServiceProvider;

class MainServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $glob = glob(__DIR__.'/../*.php');
        $classes = collect($glob)->map(function($file) 
        {
            $class = basename_without_extension($file);
            return '\\Main\\'.$class;
        });
        $classes->every(function ($class)
        {
            if (method_exists($class, 'route'))
            {
                $class::route();
            }

            return true;
        });
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
