<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use App\Models\GraduationUser;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        parent::boot();

        // Bind personalizado para graduationUser
        Route::bind('graduationUser', function ($value) {
            return GraduationUser::findOrFail($value);
        });
    }
}
