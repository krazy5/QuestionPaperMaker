<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AcademicClassModel; // <-- Import the Class model
use App\Models\Board;
class ClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {


        $maharashtraBoard = Board::where('name', 'Maharashtra State Board')->first();

        // 3. Only create the classes if the board was found.
        if ($maharashtraBoard) {
            // 4. Add the 'board_id' when creating each class.
            AcademicClassModel::create(['name' => 'Class9', 'board_id' => $maharashtraBoard->id]);
            AcademicClassModel::create(['name' => 'Class10', 'board_id' => $maharashtraBoard->id]);
            AcademicClassModel::create(['name' => 'Class11Sci', 'board_id' => $maharashtraBoard->id]);
            AcademicClassModel::create(['name' => 'Class12Sci', 'board_id' => $maharashtraBoard->id]);

        }

        $maharashtraBoard = Board::where('name', 'Mht Cet')->first();

        // 3. Only create the classes if the board was found.
        if ($maharashtraBoard) {
            // 4. Add the 'board_id' when creating each class.
          
            AcademicClassModel::create(['name' => 'Class11', 'board_id' => $maharashtraBoard->id]);
            AcademicClassModel::create(['name' => 'Class12', 'board_id' => $maharashtraBoard->id]);
          
        }
            // AcademicClassModel::create(['name' => 'Class 9']);
            // AcademicClassModel::create(['name' => 'Class 10']);
            // AcademicClassModel::create(['name' => 'Class 11']);
            // AcademicClassModel::create(['name' => 'Class 12']);
            // AcademicClassModel::create(['name' => 'Jr. College - FY']);
            // AcademicClassModel::create(['name' => 'Jr. College - SY']);
    }
}