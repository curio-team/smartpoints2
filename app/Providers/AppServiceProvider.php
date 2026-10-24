<?php

namespace App\Providers;

use App\Http\Middleware\IsTeacher;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

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
        Blade::if('teacher', function () {
            return Auth::user()?->isTeacher() ?? false;
        });

        // Livewire only re-applies route middleware on its update requests when it is
        // marked as persistent. Without this, students could call teacher components.
        Livewire::addPersistentMiddleware([
            IsTeacher::class,
        ]);
    }
}
