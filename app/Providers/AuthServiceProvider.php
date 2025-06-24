<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
/**
 * Register any authentication / authorization services.
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

        // Gate untuk semua user yang sudah login
        Gate::define('is-user', function (User $user) {
            return true;
        });

        // Gate untuk mengelola (edit/hapus) seorang user
        Gate::define('manage-user', function (User $currentUser, User $targetUser) {
            // Admin bisa kelola siapa saja, kecuali dirinya sendiri (untuk mencegah self-delete)
            if ($currentUser->level === 'Admin') {
                return $currentUser->id !== $targetUser->id;
            }
            // Master Divisi hanya bisa kelola Master User di divisinya
            if ($currentUser->level === 'Master Divisi') {
                return $targetUser->level === 'Master User' && $currentUser->division_id === $targetUser->division_id;
            }
            return false;
        });
    }
}
