<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * @var Illuminate\Support\Str
     */
    public const VALIDATION_ERROR_TITLE = "Validation error occured";

    /**
     * @var Illuminate\Support\Str
     */
    public const ERROR_TITLE =  "The process cannot be completed. (error occured)";
    
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
        //
    }
}
