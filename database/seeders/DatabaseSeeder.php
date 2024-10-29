<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(WhouseSeeder::class);
        $this->call(ReftypeSeeder::class);
        $this->call(CorpSeeder::class);
        $this->call(DeptSeeder::class);
        $this->call(SectSeeder::class);
        $this->call(EmplrSeeder::class);
        $this->call(BookSeeder::class);
    }
}
