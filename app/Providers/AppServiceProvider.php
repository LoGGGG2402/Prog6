<?php

namespace App\Providers;

use App\Helpers\FileValidator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

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

        // Force HTTPS for all URLs in production
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        // Register custom file validation rule
        Validator::extend('secure_file', function ($attribute, $value, $parameters, $validator) {
            try {
                return FileValidator::validate($value, $parameters);
            } catch (\Exception $e) {
                return false;
            }
        }, 'The :attribute must be a valid and secure file of the allowed types.');
    }
}
