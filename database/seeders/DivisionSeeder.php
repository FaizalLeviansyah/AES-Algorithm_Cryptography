<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Division; // <-- Import model Division

class DivisionSeeder extends Seeder
{
    public function run(): void
    {
        Division::create(['division_name' => 'IT Department']);
        Division::create(['division_name' => 'Finance Department']);
        Division::create(['division_name' => 'Human Resources']);
    }
}
