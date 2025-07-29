<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Board; // <-- Import the Board model

class BoardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Board::create(['name' => 'CBSE']);
        Board::create(['name' => 'ICSE']);
        Board::create(['name' => 'State Board (Maharashtra)']);
        Board::create(['name' => 'IGCSE']);
    }
}