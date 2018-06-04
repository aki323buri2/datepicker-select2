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
    	$namespace = '\\Main';
        $glob = glob(base_path('../php/main/*.php'));
        $classes = collect($glob)
        ->map(function ($file) use ($namespace)
        {
        	return $namespace.'\\'.basename_without_extension($file);
        })
        ->filter(function ($class)
        {
        	return class_exists($class);
        });
        $classes->every(function ($class)
        {
        	$class::route();
        	return true;
        });

        config([
            'database.connections' => collect(config('database.connections'))->merge([
                'ebix' => [
                    'driver' => 'mysql',
                    'host' => 'localhost', 
                    'username' => 'shokuryu', 
                    'password' => 'shokuryu', 
                    'database' => 'ebix',
                    'charset' => 'utf8mb4', 
                    'collation' => 'utf8mb4_unicode_ci', 
                    'prefix' => '', 
                ], 
            ]), 
        ]);
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
