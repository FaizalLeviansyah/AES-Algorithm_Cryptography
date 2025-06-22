<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Hapus atau beri comment User::factory(10)->create(); jika ada

        // Tambahkan baris ini:
        $this->call([
            DivisionSeeder::class,
            UserSeeder::class,
        ]);
    }
}
