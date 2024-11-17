<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Setting;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot()
    {
        // Share $setting variable to all views
        View::composer('*', function ($view) {
            $setting = Setting::first(); // Pastikan tabel settings memiliki data
            $view->with('setting', $setting);
        });
    }
}
