<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\User; // <-- IMPORT INI

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
        // === DEFINISI HAK AKSES (GATES) ===

        // Gate untuk fitur yang hanya bisa diakses Admin
        Gate::define('is-admin', function (User $user) {
            return $user->level === 'Admin';
        });

        // Gate untuk fitur yang bisa diakses Admin & Master Divisi
        Gate::define('is-admin-or-master-divisi', function (User $user) {
            return in_array($user->level, ['Admin', 'Master Divisi']);
        });

        // Gate untuk Master User (semua role bisa, karena admin & master divisi juga user)
        Gate::define('is-user', function (User $user) {
            return true;
        });
    }
}
