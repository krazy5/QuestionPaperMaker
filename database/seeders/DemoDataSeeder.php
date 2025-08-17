<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Board;
use App\Models\AcademicClassModel;
use App\Models\Subject;
use App\Models\PaperBlueprint;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding demo users and HSC Science blueprints...');

        // 1. Create Users
        $admin = User::firstOrCreate(
            ['email' => 'mohsin.mohsin6@gmail.com'],
            [
                'name' => 'Mohsin Khan',
                'password' => Hash::make('mmkmnkak'),
                'role' => 'admin'
            ]
        );

        $institute = User::firstOrCreate(
            ['email' => 'aktr000@gmail.com'],
            [
                'name' => 'Azim Khan',
                'institute_name' => 'Mak Tutorials',
                'password' => Hash::make('mmkmnkak'),
                'role' => 'institute'
            ]
        );

        $this->command->info('Admin and Institute users created.');
        $this->command->info('Admin: mohsin.mohsin6@gmail.com | Pass: mmkmnkak');
        $this->command->info('Institute: aktr000@gmail.com | Pass: mmkmnkak');

        // 2. Find Board, Class, and Subjects
        $board = Board::where('name', 'Maharashtra State Board')->first();
        
        $class = AcademicClassModel::where('name', 'Class12sci')->first();
        if (!$board || !$class) {
            $this->command->error('Maharashtra Board or Class 12 not found. Please run MaharashtraBoardSeeder first.');
            return;
        }

        $maths = Subject::where('name', 'Maths')->where('class_id', $class->id)->first();
        $physics = Subject::where('name', 'Physics')->where('class_id', $class->id)->first();
        $chemistry = Subject::where('name', 'Chemistry')->where('class_id', $class->id)->first();
        $biology = Subject::where('name', 'Biology')->where('class_id', $class->id)->first();

        // 3. Create Maths Blueprint
        if ($maths) {
            $mathsBlueprint = PaperBlueprint::create([
                'name' => 'HSC Maths Pattern',
                'board_id' => $board->id,
                'class_id' => $class->id,
                'subject_id' => $maths->id
            ]);
            $secA = $mathsBlueprint->sections()->create(['name' => 'Section A', 'instructions' => 'Q. 1 contains 8 MCQs of 2 marks each. Q. 2 contains 4 VSAQs of 1 mark each.']);
            $secA->rules()->create(['question_type' => 'mcq', 'marks_per_question' => 2, 'number_of_questions_to_select' => 8]);
            $secA->rules()->create(['question_type' => 'short', 'marks_per_question' => 1, 'number_of_questions_to_select' => 4]);

            $secB = $mathsBlueprint->sections()->create(['name' => 'Section B', 'instructions' => 'Attempt any 8 out of 12 questions.']);
            $secB->rules()->create(['question_type' => 'short', 'marks_per_question' => 2, 'number_of_questions_to_select' => 8, 'total_questions_to_display' => 12]);
            
            $secC = $mathsBlueprint->sections()->create(['name' => 'Section C', 'instructions' => 'Attempt any 8 out of 12 questions.']);
            $secC->rules()->create(['question_type' => 'long', 'marks_per_question' => 3, 'number_of_questions_to_select' => 8, 'total_questions_to_display' => 12]);
            
            $secD = $mathsBlueprint->sections()->create(['name' => 'Section D', 'instructions' => 'Attempt any 5 out of 8 questions.']);
            $secD->rules()->create(['question_type' => 'long', 'marks_per_question' => 4, 'number_of_questions_to_select' => 8, 'total_questions_to_display' => 12]);
            

            $this->command->info('Maths Blueprint Created.');
        }

        // 4. Create Physics Blueprint
        if ($physics) {
            $physicsBlueprint = PaperBlueprint::create([
                'name' => 'HSC Physics Pattern',
                'board_id' => $board->id,
                'class_id' => $class->id,
                'subject_id' => $physics->id
            ]);
            $secA_p = $physicsBlueprint->sections()->create(['name' => 'Section A', 'instructions' => 'Q. 1 contains 10 MCQs. Q. 2 contains 8 VSAQs.']);
            $secA_p->rules()->create(['question_type' => 'mcq', 'marks_per_question' => 1, 'number_of_questions_to_select' => 10]);
            $secA_p->rules()->create(['question_type' => 'short', 'marks_per_question' => 1, 'number_of_questions_to_select' => 8]);
            
            $this->command->info('Physics Blueprint Created.');
        }

        // 5. Create Chemistry Blueprint
        if ($chemistry) {
            $chemBlueprint = PaperBlueprint::create([
                'name' => 'HSC Chemistry Pattern',
                'board_id' => $board->id,
                'class_id' => $class->id,
                'subject_id' => $chemistry->id
            ]);
            // You can add specific rules for Chemistry here
            $this->command->info('Chemistry Blueprint Created.');
        }

        // 6. Create Biology Blueprint
        if ($biology) {
            $bioBlueprint = PaperBlueprint::create([
                'name' => 'HSC Biology Pattern',
                'board_id' => $board->id,
                'class_id' => $class->id,
                'subject_id' => $biology->id
            ]);
            // You can add specific rules for Biology here
            $this->command->info('Biology Blueprint Created.');
        }

        $this->command->info('Demo data seeding completed successfully!');
    }
}
