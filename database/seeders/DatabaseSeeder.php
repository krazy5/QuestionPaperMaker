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
        // User::factory(10)->create();
// Add your new seeders to the call array
        $this->call([
            //BoardSeeder::class,
            //ClassSeeder::class,
            MaharashtraBoardSeeder::class,
            DemoDataSeeder::class,
        ]);
        
    }
}
