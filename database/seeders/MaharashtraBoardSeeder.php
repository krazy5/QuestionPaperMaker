<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use App\Models\Board;
use App\Models\AcademicClassModel;
use App\Models\Subject;
use App\Models\Chapter;
use App\Models\Question;
use Illuminate\Support\Str;

class MaharashtraBoardSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Starting smart seeder from directory structure...');
        
        $dataDirectory = database_path('seeders/data');

        // Get all board directories
        $boardDirs = File::directories($dataDirectory);

        foreach ($boardDirs as $boardPath) {
            $boardName = $this->formatName(basename($boardPath));
            $board = Board::firstOrCreate(['name' => $boardName]);
            $this->command->line("Processing Board: {$board->name}");

            $classDirs = File::directories($boardPath);
            foreach ($classDirs as $classPath) {
                $className = $this->formatName(basename($classPath));
                $class = AcademicClassModel::firstOrCreate(['name' => $className]);
                $this->command->line("  Processing Class: {$class->name}");

                $subjectDirs = File::directories($classPath);
                foreach ($subjectDirs as $subjectPath) {
                    $subjectName = $this->formatName(basename($subjectPath));
                    $subject = Subject::firstOrCreate(['name' => $subjectName, 'class_id' => $class->id]);
                    $this->command->line("    Processing Subject: {$subject->name}");

                    $chapterFiles = File::files($subjectPath);
                    foreach ($chapterFiles as $chapterFile) {
                        // Remove number prefix like "01-" and the ".json" extension
                        $chapterName = $this->formatName(Str::of(basename($chapterFile, '.json'))->after('-'));
                        $chapter = Chapter::firstOrCreate(['name' => $chapterName, 'subject_id' => $subject->id]);
                        $this->command->info("      - Seeding Chapter: {$chapter->name}");

                        $questions = json_decode(File::get($chapterFile), true);
                        foreach ($questions as $qData) {
                            $this->createQuestion($qData, $board, $class, $subject, $chapter);
                        }
                    }
                }
            }
        }
        
        $this->command->info('Smart seeder completed successfully!');
    }       

    private function formatName(string $slug): string
    {
        return Str::of($slug)->replace('-', ' ')->title();
    }

    private function createQuestion(array $qData, $board, $class, $subject, $chapter): void
        {
            // normalize options to array
            $options = $qData['options'] ?? null;
            if (is_string($options)) {
                $maybe = json_decode($options, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($maybe)) {
                    $options = $maybe; // was JSON string -> array
                } else {
                    // last-resort: turn a quoted list into array
                    $options = array_filter(array_map('trim', explode('","', trim($options, "[]\""))));
                }
            }
            if (!is_array($options)) {
                $options = []; // ensure array
            }

            $details = [
                'board_id'      => $board->id,
                'class_id'      => $class->id,
                'subject_id'    => $subject->id,
                'chapter_id'    => $chapter->id,
                'question_text' => $qData['text'],
                'marks'         => $qData['marks'],
                'question_type' => $qData['type'],
                'difficulty'    => $qData['difficulty'] ?? 'medium',
                'source'        => 'admin',
                'approved'      => true,
                'options'       => $options,                 // 👈 store array (Model cast handles JSON)
                'correct_answer'=> $qData['correct_answer'] ?? null,
            ];

            if ($qData['type'] === 'true_false') {
                $details['correct_answer'] = $qData['answer'] ?? null;
            } elseif (!in_array($qData['type'], ['mcq','true_false'], true)) {
                $details['answer_text'] = $qData['answer'] ?? null;
            }

            Question::create($details);
        }

}
