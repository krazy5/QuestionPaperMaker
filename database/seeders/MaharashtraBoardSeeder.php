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
        
        $jsonPath = database_path('seeders/data/maharashtra_board_questions.json');

        if (!File::exists($jsonPath)) {
            $this->command->error("JSON file not found at: {$jsonPath}");
            return;
        }

        $data = json_decode(File::get($jsonPath), true);

        $board = Board::firstOrCreate(['name' => 'Maharashtra State Board']);
        $class = AcademicClassModel::firstOrCreate(['name' => 'Class 12 (jnr college)']);

        foreach ($data as $subjectData) {
            $subject = Subject::firstOrCreate([
                'name' => $subjectData['subject'],
                'class_id' => $class->id
            ]);
            $this->command->info("Processing Subject: {$subject->name}");

            foreach ($subjectData['chapters'] as $chapterData) {
                $chapter = Chapter::firstOrCreate([
                    'name' => $chapterData['name'],
                    'subject_id' => $subject->id
                ]);
                $this->command->info("  - Seeding Chapter: {$chapter->name}");

                foreach ($chapterData['questions'] as $qData) {
                    // Base data for every question
                    $questionDetails = [
                        'board_id' => $board->id,
                        'class_id' => $class->id,
                        'subject_id' => $subject->id,
                        'chapter_id' => $chapter->id,
                        'question_text' => $qData['text'],
                        'marks' => $qData['marks'],
                        'question_type' => $qData['type'],
                        'difficulty' => 'medium',
                        'source' => 'admin',
                        'approved' => true,
                    ];

                    // --- THIS IS THE NEW LOGIC ---
                    // Handle different question types
                    switch ($qData['type']) {
                        case 'mcq':
                            $questionDetails['options'] = json_encode($qData['options']);
                            $questionDetails['correct_answer'] = $qData['correct_answer'];
                            break;
                        case 'true_false':
                            // For T/F, the 'answer' from JSON goes into the 'correct_answer' column
                            $questionDetails['correct_answer'] = $qData['answer'];
                            break;
                        default: // 'long', 'short', etc.
                            // For other types, the 'answer' goes into 'answer_text'
                            $questionDetails['answer_text'] = $qData['answer'];
                            break;
                    }
                    
                    Question::create($questionDetails);
                }
            }
        }
        
        $this->command->info('Maharashtra State Board seeding from JSON completed successfully!');
    }
}
