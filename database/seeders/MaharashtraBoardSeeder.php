<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use App\Models\Board;
use App\Models\AcademicClassModel;
use App\Models\Subject;
use App\Models\Chapter;
use App\Models\Question;

class MaharashtraBoardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding Maharashtra State Board from JSON file...');
        
        // Path to the JSON file
        $jsonPath = database_path('seeders/data/maharashtra_board_questions.json');

        // Check if the file exists
        if (!File::exists($jsonPath)) {
            $this->command->error("JSON file not found at: {$jsonPath}");
            return;
        }

        // Read and decode the JSON file
        $data = json_decode(File::get($jsonPath), true);

        // Find or Create the Board and Class
        $board = Board::firstOrCreate(['name' => 'Maharashtra State Board']);
        $class = AcademicClassModel::firstOrCreate(['name' => 'Class 12 (jnr college)']);

        foreach ($data as $subjectData) {
            // Create the subject
            $subject = Subject::firstOrCreate([
                'name' => $subjectData['subject'],
                'class_id' => $class->id
            ]);
            $this->command->info("Processing Subject: {$subject->name}");

            foreach ($subjectData['chapters'] as $chapterData) {
                // Create the chapter
                $chapter = Chapter::firstOrCreate([
                    'name' => $chapterData['name'],
                    'subject_id' => $subject->id
                ]);
                $this->command->info("  - Seeding Chapter: {$chapter->name}");

                foreach ($chapterData['questions'] as $qData) {
                    // Create the question
                    Question::create([
                        'board_id' => $board->id,
                        'class_id' => $class->id,
                        'subject_id' => $subject->id,
                        'chapter_id' => $chapter->id,
                        'question_text' => $qData['text'],
                        'answer_text' => $qData['answer'],
                        'marks' => $qData['marks'],
                        'question_type' => 'long',
                        'difficulty' => 'medium',
                        'source' => 'admin',
                        'approved' => true,
                    ]);
                }
            }
        }
        
        $this->command->info('Maharashtra State Board seeding from JSON completed successfully!');
    }
}
