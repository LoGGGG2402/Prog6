<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Fix for MySQL older versions
        Schema::defaultStringLength(191);
        
        // Add Blade directives for user roles
        Blade::if('teacher', function () {
            return auth()->check() && auth()->user()->isTeacher();
        });
        
        Blade::if('student', function () {
            return auth()->check() && auth()->user()->isStudent();
        });
    }
}
