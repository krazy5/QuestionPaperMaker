<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AcademicClassModel; // <-- Import the Class model

class ClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AcademicClassModel::create(['name' => 'Class 9']);
        AcademicClassModel::create(['name' => 'Class 10']);
        AcademicClassModel::create(['name' => 'Class 11']);
        AcademicClassModel::create(['name' => 'Class 12']);
        AcademicClassModel::create(['name' => 'Jr. College - FY']);
        AcademicClassModel::create(['name' => 'Jr. College - SY']);
    }
}